# Az ügyfél válaszának elemzése: mit változtat a mostani rendszeren

> Az ügyfél válaszát (BMA_ajanlatkeres_struktura.docx) a mostani plugin seederével és az élő adatbázissal vetettük össze (4 szolgáltatás, 13 elem, 16 mező).

Röviden: az ügyfél válasza nem finomítja, hanem lecseréli a mostani felállást. Más lesz a szolgáltatások listája, megfordul az űrlap logikája, és két új fogalom kerül be: a tervfázis és a kiegészítő szolgáltatás. A mostani oldalt és az adatbázist vetettem össze a válaszukkal. Az élő adatbázisban 4 szolgáltatás, 13 elem és 16 mező van, pontosan a seeder szerint.

## 1. Szolgáltatások: mi lesz a mostaniakból

| Most az oldalon | Az ügyfél válasza alapján |
|---|---|
| **Tervezés** (víz-, gáz-, szennyvízbekötési terv, elektromos hálózat) | Két új főcsoportra válik szét: **Épületgépészet** (5 elem) és **Bekötések** (4 elem) |
| **Engedélyeztetés** (építési engedély, használatbavétel, önkormányzati egyeztetés) | Nem lesz önálló szolgáltatás. Minden főcsoportnál egy-egy „Engedélyeztetés” kiegészítő pipa lesz helyette |
| **Műszaki dokumentáció** (műszaki leírás, építési napló, átadási jegyzőkönyv) | **Nem szerepel** a válaszban. Legfeljebb a „Műszaki szakvélemény” és a „Tervezői művezetés” rokon vele |
| **Közmű csatlakozás koordináció** (vízmű, gázmű, áramszolgáltató) | **Nem szerepel**. Részben a „Közműszolgáltatói adatok beszerzése” kiegészítő és a Bekötések veszi át |
| – | **Új:** Tűzvédelmi rendszerek (4 elem) |
| – | **Új:** Közműhálózatok (7 elem) |
| – | **Új:** 11 általános kiegészítő szolgáltatás (pl. urbanisztikai bizonylat beszerzése, topográfiai felmérés, tanácsadás) |

A 13 elemből 20 elem lesz, plusz a kiegészítők. Ráadásul ez a struktúra már a te kérdéseidben szereplő változattól is eltér: a tűzvédelem bővült, a víz és a szennyvíz összevonva „Víz-csatorna” lett, és bekerült a csapadékvíz meg a belső gáz.

## 2. Új fogalom: a tervfázis

Ez a legnagyobb változás. Ma minden elem egy sima pipa, tervfázis nincs a rendszerben. Az új logika:

- **Egyszer, az űrlap elején** kell kiválasztani, és **minden elemre érvényes**. A lehetőségek: Engedélyes terv (DTAC), Kivitelezési terv (PT + DDE), a kettő együtt, illetve SF / DALI.
- Az **SF / DALI csak önkormányzati megrendelőnek** jelenhet meg. Ehhez új mező kell a megrendelő típusára (magánszemély / cég / önkormányzat), és **feltételes megjelenítés**. Ilyen jelenleg nincs: a dinamikus mezőkből hiányzik a feltételes logika.
- Elemenként felülírható egy **„eltérő tervfázis”** jelölőnégyzettel. Ez a kiválasztott sor saját adata lesz, nem a projekté.
- A **Bekötéseknél nincs tervfázis**, ott ez a választás nem érvényes.

Vigyázni kell a megnevezéssel: a mostani `epitesi_fazis` mező (új építés / felújítás / bővítés / átalakítás) **nem ugyanaz**. Az megmarad „új vagy meglévő” jelleggel a projektadatok között. A tervfázis egy külön, új mező.

## 3. Megfordul az űrlap sorrendje

- **Most:** a látogató szolgáltatásoldalakról „kosárba” teszi az elemeket, és csak a végén ad meg projektadatokat. A mezők a kiválasztott elemekből állnak össze.
- **Az ügyfél javaslata:** projektadatok → tervfázis → szolgáltatások → kiegészítők → dokumentumok, vagyis lépésenként haladó űrlap, ahol a projekt jön előbb.

A szolgáltatásoldalak a SEO miatt megtarthatók, és onnan lehet indítani az ajánlatkérést. Maga az ajánlatkérés viszont többlépéses, a kosáron alapuló folyamatot ennek megfelelően át kell alakítani.

## 4. Dokumentumok: az „Igen / Nem” automatikusan kijelöl

Új szabály: ha az ügyfél egy dokumentumra „Nem”-et válaszol, az nem akadályozza az ajánlatkérést. Helyette **automatikusan bejelölődik** a megfelelő beszerzési kiegészítő. Ez kapcsolatot jelent a mezőválaszok és a kiegészítők között, ami ma nincs.

- **Urbanisztikai bizonylat:** Igen (feltöltés) / Nem / Folyamatban, nem kötelező. Gyakorlatilag minden engedélyes tervhez kell, nem csak a bekötéshez.
- **Megmarad:** helyszín, helyrajzi szám (cím vagy telekkönyvi szám), alapterület, szintek, épület funkciója. A funkció most csak az engedélyeztetésnél szerepel, ezután mindenhol kötelező.
- **Szigorodik:** épületgépészetnél az alaprajz feltöltése **kötelező** lesz (ma opcionális).
- **Új mezők:**
  - fűtésnél hőforrás
  - belső gáznál a gázbekötés megléte
  - tűzvédelemnél épületmagasság és tűzvédelmi forgatókönyv
  - közműhálózatnál hálózathossz, nyomvonal, fogyasztók vagy lakosegyenérték száma, csatlakozási pont
  - bekötésnél várható fogyasztás, meglévő hálózat helye, szolgáltató neve
- **Kiesik:** vízigény, gáz típusa, teljesítményigény, dokumentáció célja, prioritás.

A mostani rendszer tudja az elemekhez kötött mezőket, így például a „csak fűtésnél” típusú mezőket. A feltételes megjelenítést és az automatikus kijelölést viszont nem tudja.

## 5. Ami még érintett

- **Szolgáltatásoldalak és URL-ek:** 4 oldal helyett 4 új lesz, más címmel. A régi URL-ekre átirányítás kell, és a RO/EN fordításokat is át kell írni.
- **Főoldal:** a „Gyors indítás” gombok ma Tervezés / Engedélyeztetés / Műszaki dokumentáció / Közmű koordináció. Ezek is cserélendők.
- **Referenciák:** a 4 demo referencia a régi szolgáltatásokra épül, pl. „Irodaház felújítás – műszaki dokumentáció”.
- **Admin és e-mailek:** az ajánlatkérés részletein meg kell jelennie a tervfázisnak és a kiegészítőknek.
- **Meglévő kérések:** az adatbázisban lévő 11 kérés a szolgáltatásnevet szövegként tárolja, ezért nem vesznek el.

## 6. Mielőtt nekiállnánk: ezeket érdemes visszakérdezni

1. **Gáz- és áramcsatlakozás:** ma a kínálat része a gázbekötési terv, a gázmű és az áramszolgáltató egyeztetés. Az új listában a bekötések között nincs se gáz, se áram. Tényleg kikerülnek?
2. **Műszaki dokumentáció és használatbavétel:** az építési napló, az átadási jegyzőkönyv és a használatbavételi eljárás teljesen kimaradt. Megszűnnek, vagy csak kifelejtették?
3. **Terv-ellenőrzés:** azt írják, hogy kivitelezési tervhez kötelező. Kivitelezési fázisnál automatikusan bekerüljön az ajánlatkérésbe, vagy csak tájékoztassunk róla?
4. **Román szakkifejezések** (DTAC, PT, DDE, SF/DALI, ISU): a magyar és az angol felületen is így jelenjenek meg, magyarázó szöveggel?