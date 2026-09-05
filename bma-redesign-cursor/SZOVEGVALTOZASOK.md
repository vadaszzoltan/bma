# Szövegváltozások – mit írtam át és mire

Ez a fájl azért készült, hogy a bekötést végző eszköz (Cursor) **ne cserélje le vakon**
a meglévő szövegeket. Minden olyan szöveget felsorol, ami a `markup/` fájlokban eltér a
jelenlegi oldalon található változattól.

## Hogyan olvasd

| Jelölés | Jelentése | Mit tegyél vele |
|---|---|---|
| **VÁLTOZATLAN** | A markupban ugyanaz a szöveg, mint a jelenlegi oldalon. | Vedd át a meglévő stringet / adatbázis-mezőt. Ne írd újra. |
| **MÓDOSÍTOTT** | Átfogalmaztam vagy javítottam. | Az új szöveget használd, de a táblázatban látod, mi volt az eredeti. |
| **ÚJ** | Nincs megfelelője a jelenlegi oldalon. | Új string, fordítandó mindhárom nyelvre. |
| **HELYŐRZŐ** | `[SZÖGLETES ZÁRÓJEL]` – nem tudtam a valós adatot. | Kérdezd meg az ügyfelet, ne találj ki értéket. |
| **JAVASLAT** | Nem írtam át, csak felvetem. | Csak akkor változtass, ha az ügyfél rábólint. |

**Alapszabály:** ami nincs ebben a fájlban felsorolva, az VÁLTOZATLAN. A markupban lévő
szolgáltatás- és referencianevek, leírások, mezőcímkék az adatbázisból jönnek – azokat
adatvezérelten kell megjeleníteni, nem beégetni.

**Fontos:** a szövegek jelenleg csak magyarul vannak meg. Minden MÓDOSÍTOTT és ÚJ stringet
le kell fordítani románra és angolra is (lásd README 4.2 – nyelvválasztó).

---

## 1. Globális, minden oldalt érintő javítások

Ezek több helyen is előfordulnak; kereséssel cserélhetők.

| # | Eredeti | Új | Miért |
|---|---|---|---|
| G1 | Közmű csatlakozás koordináció | **Közmű-koordináció** | A szolgáltatás neve három különböző alakban szerepelt az oldalon („Közmű csatlakozás koordináció", „Közmű koordináció", „közművesítés"). Egy alakra hoztam, és a magyar helyesírás szerint kötőjelezve. **Ez adatbázis-tartalom is** (szolgáltatás neve), ezért az ügyfélnek kell jóváhagynia, mielőtt a taxonómiában átírjátok. |
| G2 | Szennyvíz és csapadékvíz elvezetés tervezése | **Szennyvíz- és csapadékvíz-elvezetés tervezése** | Helyesírás: kötőjeles szóösszevonás. |
| G3 | Ivóvíz hálózatra csatlakozás és belső vízhálózat tervezése | **Ivóvízhálózatra csatlakozás és belső vízhálózat tervezése** | Helyesírás: egybeírás. |
| G4 | Ivóvíz és szennyvíz csatlakozási igény benyújtása | **Ivóvíz- és szennyvízcsatlakozási igény benyújtása** | Helyesírás. |
| G5 | Lakóingatlan bővítés | **Lakóingatlan-bővítés** | Helyesírás (referencia projekt címe – adatbázis-tartalom). |
| G6 | Kijelentkezés (fejléc gomb, kijelentkezett látogatónál is) | **Belépés** | A jelenlegi fejlécben „Kijelentkezés" áll akkor is, amikor senki nincs bejelentkezve. A tervben a gomb állapotfüggő: kijelentkezve „Belépés", bejelentkezve „Kijelentkezés". |
| G7 | — | **HU / RO / EN nyelvválasztó** | ÚJ komponens a felső sávban és a láblécben. |

**JAVASLAT (nem írtam át):** a láblécben és a „Rólunk" dobozban az `Harghita, Romania`
címsor magyar szövegkörnyezetben `Hargita, Románia` alakban is állhatna. Postai cím, ezért
meghagytam az eredetit – ti döntitek el, hogy a magyar változaton lefordítjátok-e.

---

## 2. Főoldal (`markup/01-fooldal.html`)

### Hero

| Elem | Állapot | Eredeti | Új |
|---|---|---|---|
| Nagy narancs sor | **TÖRÖLVE** | BMA ENGINEERING | – (a cégnév a fejlécben és a láblécben marad; a hero-ban a mondanivalót nyomta el) |
| H1 | MÓDOSÍTOTT | Mérnöki tervezés átlátható folyamattal | **Mérnöki tervezés *átlátható* folyamattal** (a középső szó narancs kiemelést kap, a szöveg ugyanaz) |
| Eyebrow | ÚJ | – | Tervezés · Engedélyeztetés · Közmű-koordináció |
| Bevezető | MÓDOSÍTOTT | A BMA Engineering műszaki tervezési, engedélyeztetési, dokumentációs és közmű-koordinációs szolgáltatásokat biztosít lakossági és üzleti projektekhez. Válassza ki a projektjéhez szükséges szolgáltatásokat, adja meg a rendelkezésre álló adatokat, és kérjen személyre szabott ajánlatot. | **Válassza ki, mire van szüksége a projektjéhez, adja meg a rendelkezésére álló adatokat, és kérjen személyre szabott ajánlatot. A folyamat minden lépését egy helyen követheti.** – rövidítve (a cégleírás lentebb, a „Rólunk" szekcióban változatlanul megmarad); `rendelkezésre` → `rendelkezésére` javítva |
| Gombok | VÁLTOZATLAN | Ajánlatkérés indítása · Referenciák | ugyanaz |
| „Gyors indítás" sáv | ÚJ | – | Gyors indítás – mire van szüksége? + Tervezés / Engedélyeztetés / Műszaki dokumentáció / Közmű-koordináció |
| Adatsor | ÚJ + HELYŐRZŐ | – | Odorheiu Secuiesc · Harghita · Lakossági és üzleti projektek · **Válasz [X] munkanapon belül** |

### Új sáv a hero alatt (`.valuestrip`) – mind ÚJ

- Egy ajánlatkérésben több szolgáltatás és részfeladat is összeállítható.
- A tervrajzok és dokumentumok az ajánlatkéréshez rendelve tölthetők fel.
- Az ügyfélportálon végigkövethető a kérés státusza és a dokumentumok.

### Szolgáltatások szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| H2 „Miben tudunk segíteni?" | VÁLTOZATLAN | |
| Lead | MÓDOSÍTOTT | Eredeti: „A projekt igényeihez igazodva egy vagy több szolgáltatást is kiválaszthat. Az ajánlatkérés során pontosan megadhatja, mely részfeladatokra van szüksége." → Új: **„Négy szolgáltatási terület, azon belül konkrét részfeladatokkal. Az ajánlatkérésbe bármennyi tétel bekerülhet."** (rövidebb, és megmondja, hány terület van) |
| Kártyacímek és leírások | VÁLTOZATLAN | A négy szolgáltatás neve és leírása szó szerint az eredeti (a G1 átnevezés kivételével). **Adatbázisból jöjjenek.** |
| Részfeladat-listák a kártyákon | ÚJ elrendezés, VÁLTOZATLAN szövegek | A tételnevek a `/szolgaltatasok/` oldalról származnak, változatlanul. |
| Kártya-link | MÓDOSÍTOTT | Eredeti: (nem volt link a főoldali kártyán) → Új: **Részletek és kiválasztás →** |
| Gomb | VÁLTOZATLAN | Összes szolgáltatás |
| Eyebrow „SZOLGÁLTATÁS" kártyánként | **TÖRÖLVE** | Négy egyforma „SZOLGÁLTATÁS" felirat egymás mellett nem hordozott információt. |

### „Egy projekt ritkán…" szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow, H2, 1. bekezdés | VÁLTOZATLAN | Szó szerint az eredeti. |
| 2. bekezdés | MÓDOSÍTOTT | Eredeti: „**Az ajánlatkérés során ezért több szolgáltatást és azokon belül több részfeladatot is kiválaszthat.** Nem szükséges minden műszaki részletet előre ismernie. Adja meg a rendelkezésére álló információkat és dokumentumokat, mi pedig ezek alapján felmérjük a projektet és elkészítjük az ajánlatot." → Új: ugyanez **az első mondat nélkül**. Indok: ugyanez a mondat a szekció címében („Egy projekt ritkán csak egyetlen feladatból áll") és a jobb oldali példa-dobozban is elhangzik. Ha kell, visszatehető. |
| Gomb | VÁLTOZATLAN | Szolgáltatások kiválasztása |
| Második gomb | ÚJ | Kapcsolat |
| Jobb oldali példa-doboz | ÚJ | „Példa: új lakóépület – összeállított ajánlatkérés" + 4 tétel + „4 tétel · 1 ajánlatkérés" / „Egy ajánlatban”. Ez illusztráció, nem valós ügyféladat. |

### Előnyök szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow, H2, mind a négy cím | VÁLTOZATLAN | |
| 1–3. leírás | VÁLTOZATLAN | |
| 2. leírás | MÓDOSÍTOTT | Eredeti: „…több tervezési, engedélyeztetési, **közművesítési** és dokumentációs feladat is összeállítható." → Új: „…több tervezési, engedélyeztetési, **közmű-koordinációs** és dokumentációs feladat is összeállítható." (G1 egységesítés) |
| 4. leírás | MÓDOSÍTOTT | Eredeti: „Már az ajánlatkérés során megadhatók a projekt legfontosabb adatai, **és feltölthetők a rendelkezésre álló dokumentumok,** így az ajánlatkészítés célzottabban indulhat el." → Új: ugyanez a közbevetés nélkül (a dokumentumfeltöltés a 3. pontban már szerepel). |

### Folyamat szekció

| Elem | Állapot | Eredeti | Új |
|---|---|---|---|
| Eyebrow, H2 | VÁLTOZATLAN | FOLYAMAT / Az ajánlatkérés menete | ugyanaz |
| 1. lépés címe | MÓDOSÍTOTT | 1. Válassza ki a szolgáltatásokat | **Szolgáltatások kiválasztása** |
| 2. lépés címe | MÓDOSÍTOTT | 2. Adja meg a projekt adatait | **Projekt adatai** |
| 3. lépés címe | MÓDOSÍTOTT | 3. Küldje be az ajánlatkérést | **Beküldés** |
| 4. lépés címe | MÓDOSÍTOTT | 4. Kövesse a folyamatot | **Nyomon követés** |
| | | *Indok: a nagy „01–04" számok fölött a címben lévő „1." – „4." kettős számozás volt.* | |
| 1. leírás | VÁLTOZATLAN | | |
| 2. leírás | MÓDOSÍTOTT | Töltse ki a kiválasztott szolgáltatásokhoz szükséges adatokat, és töltse fel a rendelkezésre álló tervrajzokat vagy más dokumentumokat. | **Töltse ki a kiválasztott tételekhez tartozó adatokat, és töltse fel a meglévő tervrajzokat.** |
| 3. leírás | MÓDOSÍTOTT | Adja meg kapcsolattartási adatait, ellenőrizze a kiválasztott tételeket, majd küldje el az ajánlatkérést. | **Ellenőrizze az összeállítást, adja meg a kapcsolattartási adatait, és küldje el a kérést.** (a tényleges sorrend szerint) |
| 4. leírás | MÓDOSÍTOTT | Ügyfélfiók létrehozása esetén egy helyen követheti az ajánlatkérés státuszát, és elérheti a feltöltött ajánlatokat, valamint a később elkészült dokumentumokat. | **Az ügyfélportálon látja a státuszt, az elkészült ajánlatot és a feltöltött dokumentumokat.** |
| Gomb | VÁLTOZATLAN | Ajánlatkérés indítása | |

### Referenciák szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow, H2, lead | VÁLTOZATLAN | |
| Projektcímek, kategóriák, leírások | VÁLTOZATLAN | Adatbázisból (a G5 kötőjel kivételével). |
| Szolgáltatás-felsorolás a kártyán | MÓDOSÍTOTT megjelenés | Eredetileg vesszős szöveg („Közmű csatlakozás koordináció, Tervezés”), most külön címkék – ugyanaz az adat. |
| „Projekt megtekintése" | VÁLTOZATLAN | |
| Gomb | VÁLTOZATLAN | Összes referencia |

### Rólunk szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow, H2, 1–2. bekezdés | VÁLTOZATLAN | |
| 3. bekezdés | **TÖRÖLVE a szövegtörzsből** | „Lakossági és üzleti projektekhez egyaránt biztosítunk kapcsolódó mérnöki szolgáltatásokat, az egyedi projektigényekhez igazodva." – tartalma átkerült a jobb oldali adatdobozba („Munkaterületek: Lakossági és üzleti projektek"). Ha meg akarjátok tartani a bekezdést is, tegyétek vissza. |
| Adatdoboz | ÚJ + HELYŐRZŐ | Székhely / CUI / Munkaterületek / **Nyelvek: [MAGYAR / ROMÁN]** / Nyitvatartás – a CUI, a cím és a nyitvatartás a láblécből jön, változatlanul. |
| Gomb | ÚJ | Kapcsolat |

### Záró szekció

| Elem | Állapot | Megjegyzés |
|---|---|---|
| H2 és bekezdés | VÁLTOZATLAN | |
| Eyebrow „Következő lépés" | ÚJ | |
| Gombok | VÁLTOZATLAN | Ajánlatkérés indítása · Kapcsolat |
| Kapcsolat-mondat | MÓDOSÍTOTT | Eredeti: „Ha még nem tudja pontosan, mely szolgáltatásokra van szüksége, vegye fel velünk a kapcsolatot." → Új, rövidebb: **„Ha még nem tudja pontosan, mire van szüksége, hívjon minket."** |

### Lábléc

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Cím, cím-adatok, telefon, e-mail, CUI, copyright, jogi linkek | VÁLTOZATLAN | |
| „KÖZÖSSÉGI MÉDIA" oszlop | **TÖRÖLVE** | A jelenlegi láblécben üres. Ha lesz tartalma, visszatehető. |
| Helyette: „AJÁNLATKÉRÉS" oszlop | ÚJ | „Válassza ki a szükséges szolgáltatásokat, és néhány adat megadásával kérjen személyre szabott ajánlatot." + gomb |
| Navigáció | MÓDOSÍTOTT | Kiegészült az **Ügyfélportál** linkkel. |
| Nyelvválasztó | ÚJ | HU / RO / EN |

---

## 3. Szolgáltatás lista (`markup/02-szolgaltatasok.html`)

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow, H1, lead | VÁLTOZATLAN | Szó szerint az eredeti. |
| Négy terület neve és leírása | VÁLTOZATLAN | Adatbázisból (G1 kivételével). |
| Tételnevek és rövid leírások | VÁLTOZATLAN | Mind a 13 tétel szó szerint (G2–G4 helyesírási javításokkal). |
| „EZT VÁLASSZA, HA:" blokkok | VÁLTOZATLAN | Mind a 13 szöveg szó szerint az eredeti – ezek segítenek a legtöbbet a döntésben. |
| „Részletek" gomb | VÁLTOZATLAN | |
| „Hogyan működik?" doboz | ÚJ + HELYŐRZŐ | 3 pont, köztük „Visszajelzés **[X] munkanapon belül**, kötelezettség nélkül." |
| Ugróhivatkozások (01–04 + területnevek) | ÚJ | |
| Kosár-panel: „Az Ön ajánlatkérése" | ÚJ | |
| Üres állapot | ÚJ | „Még nem választott tételt. Jelölje be bal oldalon, mire van szüksége – bármennyi tétel egy ajánlatkérésbe kerülhet." |
| Gomb | MÓDOSÍTOTT | Eredeti: „Hozzáadás és továbblépés" / „Tovább az ajánlatkéréshez" → Új: **„Tovább az ajánlatkéréshez"** (egy gomb marad; a hozzáadás kattintásra azonnal megtörténik) |
| Gomb alatti magyarázat | ÚJ | „A kiválasztás bármikor módosítható a beküldésig. Az ajánlatkérés nem jár kötelezettséggel." / üres kosárnál „Legalább egy tételt válasszon ki a továbblépéshez." |
| Alsó kártya | MÓDOSÍTOTT | Eredeti: „Több szolgáltatásra van szüksége?" → Új: **„Nem találja, amire szüksége van?"** + „Írja le röviden a projektet, és segítünk összeállítani a szükséges tételeket." (az eredeti kérdés a többes kiválasztásra utalt, ami már a lista tetején szerepel) |

---

## 4. Ajánlatkérés (`markup/03-ajanlatkeres.html`)

A jelenlegi `/ajanlatkeres/` oldalt csak üres kosárral láttam („A kosár üres. Az
ajánlatkéréshez először válasszon szolgáltatást."), a kitöltött űrlapot nem. **Az itteni
szövegek túlnyomó része ÚJ.** A mezőcímkék viszont a portál részletes nézetéből származnak,
és VÁLTOZATLANOK – ezek a valós mezőnevek.

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Lépéscímek (Válassza ki a szolgáltatásokat / Adja meg a projekt adatait / Ellenőrizze és küldje be / Ajánlatkérés elküldve) | ÚJ | |
| Lépéssáv feliratai (Szolgáltatások / Projekt adatai / Ellenőrzés és beküldés / Visszaigazolás) | ÚJ | |
| Mezőcímkék: Projekt helyszíne, Helyrajzi szám, Épület funkciója, Építési fázis, Alapterület (m²), Szintek száma, Kívánt határidő, További megjegyzés, Becsült vízigény (m³/nap) stb. | VÁLTOZATLAN | A portálon látott valós mezőnevek. |
| „Kapcsolódó korábbi ügy" mező | ÚJ | Új mező javaslata – ha nem kell, hagyd ki. |
| Feltöltő doboz szövege | ÚJ + HELYŐRZŐ | „Húzza ide a fájlokat, vagy tallózzon" / „PDF, DWG, JPG, DOCX · legfeljebb **[X] MB** / fájl" |
| Kapcsolattartás mezői (Név, Cég, E-mail, Telefon) | ÚJ | |
| Ügyfélfiók-jelölő | ÚJ | „Hozzon létre ügyfélfiókot, hogy egy helyen kövesse a kérés státuszát, az ajánlatot és a dokumentumokat." – alapból **nincs bepipálva**. |
| Adatkezelési jelölő | ÚJ | „Elolvastam és elfogadom az adatkezelési tájékoztatót." – alapból nincs bepipálva (jogi követelmény). |
| Visszaigazoló oldal | ÚJ + HELYŐRZŐ | „Köszönjük, megkaptuk az ajánlatkérését" + azonosító + három lépés: Most / **[X] munkanapon belül** / Ezután |
| Az azonosító formátuma | VÁLTOZATLAN minta | `BMA-ÉÉÉÉHHNN-XXXXXX` – a portálon látott formátum. |

---

## 5. Ügyfélportál (`markup/04-ugyfelportal.html`)

| Elem | Állapot | Eredeti | Új |
|---|---|---|---|
| Eyebrow, H1 | VÁLTOZATLAN | ÜGYFÉLTERÜLET / ÜGYFÉLPORTÁL | |
| Üdvözlés | MÓDOSÍTOTT | Üdvözöljük, {email}! | **Üdvözöljük, [ÜGYFÉL NEVE] · [email cím]** – ha van név a fiókban, azt írja ki, az e-mail másodlagos |
| Csempe 1 | MÓDOSÍTOTT | AKTÍV KÉRÉSEK | **Összes kérés** (a csempe most szűrő, a fülekkel azonos állapotra) |
| Csempe 2 | MÓDOSÍTOTT | ÚJ AJÁNLAT | **Ajánlat elérhető** |
| Csempe 3 | MÓDOSÍTOTT | ELKÉSZÜLT TERVEK | **Folyamatban** |
| Csempe 4 | VÁLTOZATLAN | FIZETÉSRE VÁR | **Megmarad** – a rendszerben van Stripe-alapú fizetés (`bma_create_stripe_session`). Korábban tévesen kivettem; javítva. |
| Fülek | ÚJ | – | Összes / Folyamatban / Ajánlat elérhető / Fizetésre vár |
| Teendő-sáv | ÚJ | – | „1 ajánlat vár elfogadásra" + „az ajánlat **[X] napig** érvényes" + „Ajánlat megtekintése" |
| Táblázat fejlécei | MÓDOSÍTOTT | AZONOSÍTÓ / DÁTUM / SZOLGÁLTATÁSOK / STÁTUSZ / AJÁNLAT | **AZONOSÍTÓ / DÁTUM / SZOLGÁLTATÁSOK / STÁTUSZ** – az „AJÁNLAT" oszlop beolvadt a státuszba |
| Státusz-szövegek | VÁLTOZATLAN | Ajánlat elkészült / Elfogadva / Ajánlat elérhető / Fizetésre vár | ugyanazok |
| Fizetésre váró sor következő lépése | ÚJ | – | „Teendő: díjbekérő rendezése" |
| Sor alatti következő lépés | ÚJ | – | „Teendő: ajánlat elfogadása" / „Tervek készítése folyamatban" |
| „Dokumentumaim" panel | ÚJ | | |
| „Segítségre van szüksége?" panel | ÚJ | | |
| Részletes nézet: „← Vissza a portálra", „AJÁNLATKÉRÉS", „Kérés részletei, státusz, dokumentumok és ajánlat.", „SZOLGÁLTATÁSOK", „PROJEKT ADATOK", „BEKÜLDÖTT DOKUMENTUMAIM", „További dokumentum feltöltése", „Fájl", „Dokumentumtípus", „Feltöltés", „AJÁNLAT", „Ajánlat száma", „Érvényes", „Ajánlati PDF letöltése", „ELKÉSZÜLT TERVEK", „STÁTUSZELŐZMÉNYEK" | VÁLTOZATLAN | | Mind az eredeti feliratok. |
| „Kiválasztott tételek" panelcím | MÓDOSÍTOTT | SZOLGÁLTATÁSOK | **Kiválasztott tételek** (a fejlécben lévő menüponttal való ütközés miatt) |
| Tételenkénti státusz | MÓDOSÍTOTT | Még nincs feltöltött terv ehhez a tételhez. | **Tervezés folyamatban** / **Dokumentáció összeállítása** / **Ajánlat elfogadására vár** – a tétel állapotát mondja meg, nem a hiányt |
| „Módosítás kérése" link | ÚJ | | |
| Projekt adatok | VÁLTOZATLAN mezőnevek + HELYŐRZŐ értékek | | |

---

## 6. Referenciák lista (`markup/05-referenciak.html`)

**A `/referenciak/` oldalt nem tudtam megnyitni**, ezért ennek az oldalnak a szövegei
a főoldali referencia-szekcióból és általános mintából származnak. Minden itteni szöveget
vessetek össze a meglévő oldallal, mielőtt lecserélitek.

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Eyebrow „Projektek", H1 „Referenciák" | VÁLTOZATLAN (a főoldali szekcióból) | |
| Lead | MÓDOSÍTOTT | A főoldali „Válogatás megvalósult tervezési, engedélyeztetési és közmű-koordinációs munkáinkból." bővítve: **„…Szűrjön projekttípus vagy szolgáltatás szerint."** |
| Szűrő feliratai: Típus / Összes / Lakossági / Üzleti; Szolgáltatás / Összes / Tervezés / Engedélyeztetés / Dokumentáció / Közmű | ÚJ | A típus- és szolgáltatáscímkék taxonómiából jöjjenek. |
| Darabszám („4 projekt") | ÚJ | |
| Üres állapot | ÚJ | „Ezzel a szűréssel nincs találat. Próbáljon másik típust vagy szolgáltatást, vagy állítsa vissza a szűrőt »Összes«-re." |
| Projektcímek, kategóriák, leírások | VÁLTOZATLAN | A főoldalon szereplő négy projekt szövege, szó szerint. |
| Adatbázis-megjegyzés | ÚJ | Belső magyarázat, élesben törölhető. |
| Záró sáv | ÚJ | „Hasonló projektje van?" + szöveg + gombok |

---

## 7. Referencia projekt oldal (`markup/06-referencia-projekt.html`)

**Ezt az oldaltípust nem láttam.** A szerkezet javaslat, a törzsszöveg végig HELYŐRZŐ –
szándékosan nem írtam bele kitalált projektleírást.

| Elem | Állapot | Megjegyzés |
|---|---|---|
| Projektcím, kategória, bevezető | VÁLTOZATLAN | A főoldali kártya szövege szó szerint. |
| „Projekt adatai" doboz | ÚJ, de **adatvezérelt** | Helyszín → `_bma_location`, Típus → `_bma_project_type`, Év → `_bma_year`, Szolgáltatások → `_bma_related_services`. A markupban lévő `[TELEPÜLÉS]` és `[ÉVSZÁM]` **NEM helyőrző** – ezekből a metákból töltendő. |
| „A feladat", „Amit csináltunk", „Eredmény", „Képek" címek | ÚJ | |
| A három szövegtörzs | HELYŐRZŐ | `[A projekt kiindulási helyzete…]` stb. – **az ügyfél tölti ki**, ne generáljatok bele szöveget |
| „Amit csináltunk" sorai | ÚJ | Közmű-koordináció / Tervezés / Dokumentáció / Átfutás **[X] hónap** |
| „Ehhez kapcsolódó szolgáltatások" panel + „Hozzáadás" gombok | ÚJ | Ez köti össze a referenciát az ajánlatkéréssel. |
| „További projektek" panel | ÚJ | |
| Záró sáv „Hasonló projektet tervez?" | ÚJ | |
| Doboz alatti megjegyzés | ÚJ | „A projekt részletei az ügyféllel egyeztetett mértékben nyilvánosak." – jogi óvatosság, törölhető |

---

## 8. Szolgáltatás aloldal (`markup/07-szolgaltatas-aloldal.html`)

A `/szolgaltatas/tervezes/` oldal létezik; az alábbiak ahhoz képest értendők.

| Elem | Állapot | Eredeti | Új |
|---|---|---|---|
| Eyebrow, H1 | VÁLTOZATLAN | SZOLGÁLTATÁS / TERVEZÉS | |
| Bevezető | MÓDOSÍTOTT | Ha új épületet, átalakítást vagy közműcsatlakozást tervez, és az ezekhez szükséges műszaki tervekre van szüksége, ezt a szolgáltatást válassza. | **Közmű- és épületgépészeti tervek lakossági és üzleti projektekhez, a helyszíni adottságok figyelembevételével. A tervezés indulhat önállóan, vagy engedélyeztetéssel és közmű-koordinációval együtt, egy ajánlatkérésben.** – az eredeti első mondat a „Mikor lehet erre szüksége?" listával duplázott; helyette a szolgáltatás hivatalos leírása áll |
| Két magyarázó bekezdés („A tervezési szolgáltatásunk…", „A folyamat során…") | **NEM SZEREPEL a tervben** | | Nem töröltem, csak nem fért az új szerkezetbe. **Javaslom visszatenni** a „Mit tartalmaz a tervezés?" szekció alá, vagy beolvasztani a GYIK-ba. Döntsétek el. |
| „Mikor lehet erre szüksége?" lista (5 pont) | **ÁTALAKÍTVA** | Egy általános lista a szolgáltatásra. | Tételenkénti **„Ezt válassza, ha:"** szövegekké bontva – ezek a `/szolgaltatasok/` oldalról származnak, változatlanul. Az eredeti öt pont tartalma lefedve, de célzottabban. Ha ragaszkodtok az általános listához, visszatehető a bevezető alá. |
| „Röviden" doboz | ÚJ + HELYŐRZŐ | | 4 választható részfeladat / Lakossági és üzleti projektekhez / Ajánlat **[X] munkanapon belül** / Tipikus átfutás **[X–Y] hét** |
| „Mire lesz szükség?" szekció | ÚJ, **adatvezérelt** | | Kötelező / Ha rendelkezésre áll / Később is pótolható – a markupban lévő minta-tartalom NEM kerül be a kódba, a három `_bma_needs_*` metából jön. Ha egyik sincs kitöltve, a szekció kimarad. |
| „Hogyan zajlik?" 4 lépés | ÚJ | | Ajánlatkérés / Felmérés és ajánlat / Tervezés / Átadás |
| GYIK | ÚJ, **adatvezérelt** | | A markupban lévő 4 kérdés csak minta – élesben a `_bma_faq` metából jön, `<details>` elemekben. Ha üres, a szekció kimarad. |
| Záró sáv | ÚJ | | „Adja hozzá a tervezést az ajánlatkéréséhez" |

---

## 9. Összes helyőrző egy helyen

| Helyőrző | Hol | Mit kell megadni |
|---|---|---|
| `[X] munkanapon belül` | főoldal hero, szolgáltatás lista, aloldal, ajánlatkérés visszaigazolás | tényleges válaszidő |
| `[X–Y] hét` | szolgáltatás aloldal | tipikus átfutási idő szolgáltatásonként |
| `[X] MB` | ajánlatkérés, portál | fájlméret-korlát |
| `[MAGYAR / ROMÁN]` | főoldal adatdoboz | kiszolgálási nyelvek |
| `[X] napig érvényes`, `[ÖSSZEG] EUR` | portál | ajánlat érvényessége és összege |
| `[HELYRAJZI SZÁM]`, `[X] m²`, `[X] m³/nap` | portál részletes nézet | mintaadatok |
| `[ÜGYFÉL NEVE]`, `[email cím]` | portál | bejelentkezett felhasználóból |
| `[LOGÓ]` | fejléc, lábléc | logófájl |
| `[Projekt fotó]`, `[Fotó 1–3]`, `[Fő projektfotó]` | referenciák | képek |
| `[X] hónap` | referencia projekt | átfutási idő |
| ~~`[TELEPÜLÉS]`, `[ÉVSZÁM]`~~ | referencia projekt | **már nem helyőrző** – `_bma_location` és `_bma_year` metából jön |
| `[A projekt kiindulási helyzete…]` és a többi zárójeles bekezdés | referencia projekt | projektleírások |
| GYIK válaszok | szolgáltatás aloldal | ügyfél írja – `_bma_faq` meta, `K: …` / `V: …` formátum |
| „Mire lesz szükség?" három oszlopa | szolgáltatás aloldal | ügyfél írja – `_bma_needs_required`, `_bma_needs_optional`, `_bma_needs_later` meták, soronként egy tétel |

---

## 10. Amit semmiképp ne írj át

- A **négy szolgáltatás** és a **13 részfeladat** neve és leírása (a G1–G4 helyesírási
  javításokon túl) – ezek adatbázis-tartalom.
- A **„Ezt válassza, ha:"** szövegek – mind a 13 az eredeti.
- A **portál mezőcímkéi** és **státusz-szövegei**.
- A **cégadatok**: név, cím, telefon, e-mail, CUI, nyitvatartás.
- A **jogi linkek**: Adatvédelem · Cookie tájékoztató · ÁSZF.
- A **referencia projektek** címei és leírásai (a G5 kötőjelen túl).
