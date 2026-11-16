# Tesztelés

Az ellenőrzések a fejlesztői containerekben futnak, ezért előbb ezeket kell elindítani:

```sh
docker compose up -d --build api-dev client-dev db
```

## Mit tesztelünk és miért

A logika a backendben van, ezért az automata tesztek ott vannak. A tesztek adatbázis nélkül futnak: a repository-kat mock vagy stub helyettesíti. Így gyorsak (néhány tized másodperc), és nem függnek az adatbázis állapotától.

| Tesztfájl | Mit ellenőriz | Miért fontos |
|---|---|---|
| `Domain/DateRangeTest` | dátum formátum, nem létező nap, fordított sorrend, 366 napos határ, átalakítás `ÉÉÉÉHHNN` formára | minden kérés ezen megy át, itt van a legtöbb hibalehetőség |
| `Domain/DailyWeatherTest` | átlaghőmérséklet számítás és kerekítés, hiányzó érték kezelése, adatbázis sor átalakítása | a követelmény szerint az átlag a min és max értékből számolódik |
| `Domain/StatisticTest` | medián páros és páratlan elemszámmal, módusz egy vagy több értékkel, ismétlődés nélkül, üres lista | a lábléc értékei innen jönnek, sok határeset van |
| `Domain/UnitSystemReportTest` | a teljes táblázat és statisztika egy mértékegységrendszerben, hiányzó értékek kihagyása, statisztika az átváltott értékeken | ez a válasz szerkezete, amit a kliens megjelenít |
| `Units/UnitSystemTest` | °C és °F, mm és inch átváltás, kerekítés | az extra feladat helyessége |
| `Http/QueryParamsTest` | hiányzó, üres, tömbként érkező paraméter, nem pozitív egész szám | a hibás bemenet 400-as választ ad, nem 500-ast |
| `Http/HttpErrorHandlerTest` | a kivételekből 400-as vagy 404-es válasz lesz, a többi hiba továbbmegy | egységes hibaválaszok |
| `Filters/LocationFilterTest` | a szűrők SQL feltétele és paraméterei | a város és a régió lekérdezés ezen alapul |
| `Repositories/MySqlWeatherRepositoryTest` | a `-999` kiszűrése az SQL-ben, a paraméterek sorrendje (a szöveges paraméter az utolsó), a sorok átalakítása | a keretrendszer szöveges cseréje miatt a sorrend biztonsági kérdés |
| `Controllers/WeatherControllerTest` | teljes városos válasz, a megfelelő szűrő átadása, 404 nem létező helyre, hiányzó paraméter | a végpontok viselkedése adatbázis nélkül |

## Backend parancsok

### Minden teszt egyben

```sh
docker compose exec -e XDEBUG_MODE=off api-dev vendor/bin/phpunit
```

### Egy tesztfájl

```sh
docker compose exec -e XDEBUG_MODE=off api-dev vendor/bin/phpunit test/Domain/StatisticTest.php
```

### Egy mappa (részegység)

```sh
docker compose exec -e XDEBUG_MODE=off api-dev vendor/bin/phpunit test/Domain
```

### Egy teszteset név alapján

```sh
docker compose exec -e XDEBUG_MODE=off api-dev vendor/bin/phpunit --filter rejectsInvalidInput
```

### Statikus elemzés és formázás

```sh
docker compose exec -e XDEBUG_MODE=off api-dev composer analyse   # PHPStan, level 8
docker compose exec -e XDEBUG_MODE=off api-dev composer format    # php-cs-fixer, javít is
```

Csak ellenőrzés, módosítás nélkül:

```sh
docker compose exec -e XDEBUG_MODE=off -e PHP_CS_FIXER_IGNORE_ENV=1 api-dev vendor/bin/php-cs-fixer fix --dry-run --diff
```

Az egyetlen jelzett fájl a kapott keretrendszer `Framework/HttpRequest.php` fájlja: egy sorvégi szóköz egy kommentben. Ezt szándékosan nem módosítottam, mert nem a saját kódom része.

## Frontend parancsok

A kliensben nincs számítási logika, ezért nincs hozzá unit teszt. A minőséget típusellenőrzés, lint és formázás-ellenőrzés biztosítja, a működést a lenti e2e lépések.

```sh
docker compose exec client-dev npx tsc -b --noEmit      # TypeScript típusellenőrzés
docker compose exec client-dev npm run lint             # ESLint
docker compose exec client-dev npx prettier --check src # formázás ellenőrzése
docker compose exec client-dev npm run build            # éles build próba
```

## Minden ellenőrzés egyben

```sh
docker compose exec -e XDEBUG_MODE=off api-dev sh -c "vendor/bin/phpunit && composer analyse" \
  && docker compose exec client-dev sh -c "npx tsc -b --noEmit && npm run lint && npx prettier --check src"
```

## Kézi e2e teszt

Indítsd el a prod változatot (`docker compose up --build prod db`), és nyisd meg a http://localhost:8300 címet.

1. **Város:** Budapest, 2023. 01. 01. – 2023. 01. 31., Lekérdezés. Elvárt: 31 sor. Az első sor 3,4 °C, 8,5 °C, 6,0 °C, 0,0 mm. Medián: 2,9 °C, 7,7 °C, 5,3 °C, 0,1 mm.
2. **Régió:** Dunántúl, 2023. 07. 01. – 2023. 07. 10. Elvárt: Keszthely, Pécs, Sopron, Szombathely; 10 nap. Medián: 16,4 °C, 28,2 °C, 22,2 °C, 0,25 mm.
3. **Mértékegység:** kattints a °F, in gombra. Elvárt: azonnali váltás új lekérés nélkül. Az első sor 61,5 °F, 79,0 °F, 70,3 °F, 0,01 in.
4. **Hiányzó adat:** Szombathely (Észak és Alföld), 2014. 05. 28. Elvárt: minden érték „–”. Ugyanez a nap az Észak és Alföld régióra: a maximum 24,1 °C, a hiányzó város kimarad az átlagból.
5. **Dátumkorlátok:** kezdődátum 2023. 07. 01. Elvárt: a végdátum naptárában a korábbi napok nem választhatók. 2020. 01. 01. kezdődátumnál 2020. 12. 31. az utolsó választható nap. Ha érvénytelen dátumot gépelsz be, a mező piros lesz, és a gomb nem indít lekérdezést.
6. **API hibák:**
   ```sh
   curl -i "localhost:8300/api/v1/weather/city?cityId=99&from=2023-01-01&to=2023-01-03"   # 404
   curl -i "localhost:8300/api/v1/weather/city?cityId=abc&from=2023-01-01&to=2023-01-03"  # 400
   ```
