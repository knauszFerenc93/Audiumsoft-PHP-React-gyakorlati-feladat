# Adatbázis struktúra módosítás javaslatok

## Adathibák

### A 10-es azonosítójú város neve hibás

A cities táblában a 10-es azonosítójú város neve Szombathely, a régiója Észak és Alföld. Emiatt a városlistában kétszer szerepel Szombathely, és a felhasználó nem tudja megkülönböztetni a két várost. Javaslat: a 10-es rekord nevét javítani, és a name oszlopra egyedi indexet kell tenni, hogy ilyen hiba ne fordulhasson elő újra.

### Hiányzó mérések -999 értékkel

A forrásadatok a hiányzó méréseket -999 értékkel jelölik, ami problémás, mert a -999 érvényes számként viselkedik: minden átlag, minimum, medián vagy összeg számítást elront, ha a lekérdezés nem szűri ki külön. Javaslat: a hiányzó értéket NULL jelölje, az oszlopok legyenek NULL értéket elfogadók, a meglévő -999 értékeket pedig egy egyszeri UPDATE utasítás alakítsa NULL értékre. Így az AVG és a többi összesítő függvény magától kihagyja a hiányzó méréseket.

### Részlegesen hiányzó napok

Vannak olyan napok, amikor egy városnál csak a minimum vagy csak a maximum hőmérséklet hiányzik. Ezeken a napokon a napi átlaghőmérséklet nem számolható. Ez önmagában nem hiba, de érdemes dokumentálni, és a felületen jelezni, hogy az adott napra nincs teljes adat.

### Logikailag ellentmondó mérés

Túrkevénél 1922. december 20-án a minimum hőmérséklet 2,5 fok, a maximum 0,4 fok, vagyis a minimum nagyobb a maximumnál. Ez valószínűleg rögzítési hiba a forrásban. Javaslat: ezt a sort meg kell jelölni vagy NULL értékre kell állítani. 

## Sémahibák

### Hiányzó elsődleges kulcs és egyediség a mérési táblákban

A három mérési táblának nincs elsődleges kulcsa, és a város és dátum párosra sincs egyedi megkötés. Az import script többszöri futtatása így csendben megduplázná az adatokat, és a lekérdezések eredménye hibás lenne. Javaslat: a (city_id, date) pár legyen az elsődleges kulcs.

### Hiányzó idegen kulcsok

A mérési táblák city_id oszlopa nem hivatkozik a cities táblára. Így be lehet szúrni nem létező városhoz tartozó mérést, és törölni lehet olyan várost, amelyhez mérések tartoznak. Javaslat: FOREIGN KEY megkötés a cities.id oszlopra.

### Hiányzó indexek

A mérési táblákon nincs index. Minden lekérdezés a teljes táblát olvassa végig, és a táblák összekapcsolása is index nélkül történik. A javasolt elsődleges kulcs egyben a leggyakoribb szűrési mintát is lefedi, mert minden lekérdezés városra és dátumtartományra szűr.

### A dátum bigint típusú

A dátum ÉÉÉÉHHNN formátumú egész számként van tárolva. Az adatbázis így nem tudja ellenőrizni, hogy valós dátumról van-e szó. A dátumfüggvények csak konverzióval használhatók, és minden kliensnek ismernie kell ezt a formátumot. Javaslat: DATE típus. Az átalakítás egyszerű, mert a MySQL a jelenlegi értékeket közvetlenül dátummá tudja konvertálni.

### Float típus a mért értékekhez

A hőmérséklet és a csapadék FLOAT típusú. A FLOAT bináris közelítést tárol, ezért egy pontos egyezésre szűrő lekérdezés, például max_temp = 26.1, nem adja vissza azt a sort, amelyben 26,1 szerepel. A mérések egy tizedesjegy pontosságúak, ezért a DECIMAL(4,1) típus pontosan, kerekítési hiba nélkül tárolja őket.

### A régió ENUM típusú

A régiók listája az oszlop típusdefiníciójában van rögzítve. Új régió felvétele vagy egy régió átnevezése sémamódosítást igényel, a régióhoz pedig nem lehet további adatot rendelni, például megjelenítési sorrendet vagy hivatalos kódot. Javaslat: külön regions tábla azonosítóval és névvel, a cities táblában pedig region_id idegen kulccsal.

### Három külön mérési tábla

A minimum hőmérséklet, a maximum hőmérséklet és a csapadék ugyanahhoz a városhoz és naphoz tartozik, mégis három külön táblában van. Minden napi lekérdezéshez három táblát kell összekapcsolni, és semmi sem biztosítja, hogy mindhárom táblában ugyanazok a napok szerepeljenek. Javaslat: egyetlen daily_weather tábla city_id, date, min_temp, max_temp és precipitation oszlopokkal, (city_id, date) elsődleges kulccsal. Ez egyszerűbb lekérdezéseket és egy sorra vonatkozó ellenőrzéseket tesz lehetővé.

### A városok azonosítója kézzel kiosztott

A cities.id oszlop nem AUTO_INCREMENT, az azonosítókat az import script és a beszúró SQL kézzel rögzíti. Javaslat: AUTO_INCREMENT azonosító.

## Üzemeltetési és biztonsági javaslatok

### Szövegösszefűzéses paraméterbehelyettesítés

A keretrendszer MySQL osztálya a paramétereket szöveges cserével illeszti be az SQL utasításba. Javaslat: PDO prepare és execute használata kötött paraméterekkel.

## Átállás menete

Első lépésként létre kell hozni az új táblákat a javaslatok szerint. Ezután a meglévő három mérési táblából egyetlen INSERT ... SELECT utasítás tölti fel a daily_weather táblát, a dátumot DATE típusra alakítva és a -999 értékeket NULL értékre cserélve. A Szombathely ismétlődést és az ellentmondó 1922-es sort ebben a lépésben kell javítani. Az ellenőrzés után a régi táblák törölhetők.

## Hatás az alkalmazásra

Az API-ban az adatbázis szerkezetét csak a repository osztályok ismerik, így az átállás a többi réteget nem érinti. Az új sémán a napi lekérdezés egyetlen táblából, összekapcsolás és NULLIF nélkül működik, a dátumszűrés pedig közvetlenül DATE értékekkel történhet a jelenlegi ÉÉÉÉHHNN formátumú egész számok helyett.
