# Indítás, elérés és használat

## Előfeltételek

- Docker és Docker Compose (legalább 2.17, a `dockerfile_inline` miatt)
- A `.env` fájl létrehozása: `cp .env.example .env`
- A mintaadatok kicsomagolása: `cd migrations && unzip data.zip`

Linuxon a felhasználónak a `docker` csoportban kell lennie (`sudo usermod -aG docker $USER`, utána újra be kell jelentkezni), különben a parancsok `permission denied` hibával leállnak.

## Indítás

### Éles (prod) változat

Ezzel történik az értékelés: egyetlen container szolgálja ki a lefordított klienst és az API-t.

```sh
docker compose up --build prod db
```

Az első indításkor a `db` container betölti a CSV fájlokat, ez néhány percig tart. Akkor kész, ha a logban megjelenik a `ready for connections` üzenet.

### Fejlesztői változat

Külön container fut az API-nak és a kliensnek, mindkettő a forrásmappát csatolja, így a módosítások azonnal érvényesek.

```sh
docker compose up --build api-dev client-dev db
```

Az `api-dev` indításkor `composer install`-t, a `client-dev` `npm ci`-t futtat. Ehhez a containernek írási joga kell legyen az `api/` és `client/` mappákhoz. Az `api-dev` `nobody` felhasználóként fut, ezért Linuxon szükség lehet erre:

```sh
setfacl -R -m u:65534:rwX -m d:u:65534:rwX api
```

### Leállítás

```sh
docker compose down
```

## Elérés

| Mi | Prod | Fejlesztői |
|---|---|---|
| Webes felület | http://localhost:8300 | http://localhost:5173 |
| API | http://localhost:8300/api/v1 | http://localhost:8080 |
| MySQL (root / root) | localhost:8081 | localhost:8081 |

### API végpontok

| Végpont | Paraméterek | Leírás |
|---|---|---|
| `GET /cities` | – | városok listája |
| `GET /regions` | – | régiók listája |
| `GET /weather/city` | `cityId`, `from`, `to` | egy város napi adatai |
| `GET /weather/region` | `region`, `from`, `to` | egy régió városainak napi átlaga |

A dátumok formátuma `ÉÉÉÉ-HH-NN`, az időszak legfeljebb 366 nap. Példa:

```sh
curl "localhost:8300/api/v1/weather/city?cityId=1&from=2023-01-01&to=2023-01-31"
```

A válasz mindkét mértékegységrendszerben (`metric`, `imperial`) tartalmazza a napi sorokat, valamint a medián és módusz értékeket. Hibás kérésre 400, nem létező városra vagy régióra 404 választ ad, `{"error": "..."}` törzzsel.

## Használat

1. Felül bal oldalt válaszd ki a felületet: **Város** vagy **Régió**.
2. Válaszd ki a várost vagy a régiót, majd a kezdő- és a végdátumot. A dátumválasztó csak érvényes időszakot enged: a végdátum nem lehet korábbi a kezdődátumnál, és az időszak legfeljebb 366 nap. Az adatok 1901. 01. 01. és 2023. 12. 31. között érhetők el.
3. Kattints a **Lekérdezés** gombra. Megjelenik a napi táblázat a minimum-, maximum- és átlaghőmérséklettel, valamint a csapadékkal. A lábléc az oszlopok mediánját és móduszát mutatja.
4. Felül jobb oldalt a **°C, mm** és **°F, in** gombbal válthatsz mértékegységet. A váltás azonnali, nem indít új lekérdezést.

A hiányzó mérések „–” jellel jelennek meg, és kimaradnak a régiós átlagból, a mediánból és a móduszból.

## Ismert jelenségek

- A `prod` és az `api-dev` container állapota `unhealthy`. Az alap image (`trafex/php-nginx`) beépített healthcheckje a 8080-as portot figyeli, ezek a containerek viszont más porton futnak. A működést ez nem befolyásolja.
- A 10-es azonosítójú város az adatbázisban Szombathely néven szerepel, de valójában Túrkeve. A felület ezért a város mellett a régiót is kiírja.
