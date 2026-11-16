# Az alkalmazás struktúrája

## Alapelvek

- **A backend számol, a frontend megjelenít.** Az átlaghőmérséklet, a régiós átlag, a medián, a módusz és a mértékegység-átváltás mind a PHP oldalon készül. A kliens így egyszerű marad, és a számítások egy helyen tesztelhetők.
- **Egy válaszban minden szükséges adat.** Az API mindkét mértékegységrendszert előre kiszámolva küldi, ezért a °C és °F közötti váltás azonnali, nincs új lekérés.
- **Egy dolog, egy hely.** Minden osztály és függvény egy feladatot lát el. Ami a város és a régió esetén közös, az egyszer van megírva, a különbség konfigurációban vagy egy kis szűrőosztályban van.

## Backend (`api/src`)

```
index.php                 belépési pont: függőségek összeállítása, route-ok
Framework/                a feladathoz kapott keretrendszer (változatlan)
Controllers/              HTTP réteg: paraméterek beolvasása, válasz összeállítása
Http/                     QueryParams (típusos query-olvasás), HttpErrorHandler
Exceptions/               ClientException -> InvalidInputException (400), NotFoundException (404)
Filters/                  LocationFilter: CityFilter, RegionFilter, AllCitiesFilter
Repositories/             adatbázis-hozzáférés interface-ek mögött
Domain/                   üzleti logika és a válasz szerkezete
Units/                    mértékegységrendszerek: MetricSystem, ImperialSystem
```

### Kérés útja

1. A `Router` a `HttpErrorHandler`-rel becsomagolt controller metódust hívja meg.
2. A `WeatherController` a `QueryParams` segítségével beolvassa a paramétereket, létrehoz egy szűrőt (`CityFilter` vagy `RegionFilter`) és egy `DateRange`-et.
3. A `MySqlLocationRepository` megkeresi a szűrőhöz tartozó városokat. Ha nincs ilyen, `NotFoundException` keletkezik, ebből 404-es válasz lesz.
4. A `MySqlWeatherRepository` egyetlen SQL lekérdezéssel napi átlagot számol a szűrt városokra. Egy városnál az átlag maga az érték, ezért a város és a régió ugyanazt a lekérdezést használja. A `-999` jelölésű hiányzó értékek `NULL`-ként kimaradnak.
5. A `WeatherReport` mértékegységrendszerenként egy `UnitSystemReport`-ot készít, amely kiszámolja az átváltott napi sorokat és a statisztikákat.
6. A válasz JSON-ként megy vissza.

### Miért így

- **Szűrő objektumok (`LocationFilter`).** A városos és a régiós lekérdezés csak a `WHERE` feltételben különbözik. A szűrő adja a feltételt és a paramétereit, így a repository és a controller kódja közös.
- **Repository interface-ek.** Az adatbázis szerkezetét (három tábla, `ÉÉÉÉHHNN` dátum, `-999`) csak a repository ismeri. Ha a séma a `db-suggestions.md` szerint javul, csak itt kell módosítani. Az interface-ek miatt a controller adatbázis nélkül tesztelhető.
- **Enumok (`WeatherColumn`, `Statistic`).** A táblázat oszlopai és a lábléc statisztikái felsorolások. Új oszlop vagy statisztika egy új eset, a többi kód változatlan.
- **`UnitSystem` interface.** Új mértékegységrendszerhez elég egy új osztály, és az `index.php`-ban hozzáadni a listához.
- **Kivételek státuszkóddal.** A tartomány-logika (pl. `DateRange`) nem tud a HTTP-ről, csak kivételt dob. A `HttpErrorHandler` alakítja ezt 400-as vagy 404-es válasszá.
- **Biztonság.** A keretrendszer a paramétereket szöveges cserével helyettesíti be. Ezért a szöveges paraméter mindig az utolsó a paraméterlistában, a számok pedig `int` típusúak, így a behelyettesítés nem módosíthatja a lekérdezést.

## Frontend (`client/src`)

```
main.tsx                  belépési pont
types.ts                  az API válaszainak típusai
pages.ts                  a két felület (Város, Régió) konfigurációja
api/                      fetch hívások és hibaüzenet kezelés
hooks/                    useApiData (listák betöltése), useWeatherReport (lekérdezés állapota)
lib/                      format.ts (szám + mértékegység, dátum), dateBounds.ts (dátumválasztó korlátai)
components/               App, WeatherPage, WeatherQueryForm, WeatherTable, SegmentedControl, Field, ErrorMessage
components/weatherColumns.ts  oszlopok, statisztikák és mértékegységek feliratai
```

### Miért így

- **Egy oldal komponens, két konfiguráció.** A Város és a Régió felület ugyanaz a `WeatherPage`, csak a `pages.ts`-ben megadott lista-betöltő és lekérdező függvény különbözik.
- **A táblázat konfigurációból épül.** A `weatherColumns.ts` sorolja fel az oszlopokat és a statisztikákat, a `WeatherTable` ezeken megy végig.
- **Nincs számolás a kliensben.** A kliens csak kiválasztja a megfelelő mértékegységű adatot és formázza. Egyetlen kivétel a dátumválasztó korlátja (`dateBounds.ts`), mert ez közvetlenül a beviteli mezőt vezérli.
- **Állapotgép a lekérdezéshez.** A `useWeatherReport` állapota pontosan egy a négy közül (`idle`, `loading`, `error`, `success`), így a felület nem kerülhet ellentmondásos állapotba.
- **Router nélkül.** Két felülethez a fülek elegendők, nincs szükség külön könyvtárra.
- **Tailwind CSS** a formázáshoz, ahogy a feladat javasolja.

## Docker

A `docker/` mappa a feladat szerint nem módosítható. A `docker/api-dev.Dockerfile` és a `docker/prod.Dockerfile` jelenlegi formájában nem épül fel: az `apk add composer` egy újabb `php83-iconv` csomagot húz be a régebbi PHP mellé, az iconv nem töltődik be, és a `composer install` elbukik. Ezért a `docker-compose.yaml` `dockerfile_inline` mezőben tartalmazza a két Dockerfile másolatát, egyetlen eltéréssel: `apk upgrade --no-cache` a csomagtelepítés előtt.
