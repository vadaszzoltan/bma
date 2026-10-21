# Mi változott és miért

A jelenlegi oldalt böngészőn keresztül néztem végig (főoldal, `/szolgaltatasok/`,
`/ajanlatkeres/`, `/ugyfelportal/` és egy kérés részletes nézete). Az alábbi lista
csak azt tartalmazza, amit konkrét megfigyelésre alapoztam.

## Főoldal

| Megfigyelés | Változtatás |
|---|---|
| A hero-ban a narancs „BMA ENGINEERING" 45,6 px / 800, a tényleges üzenet (H1) viszont csak 29,6 px. A cégnév nyomja el a mondanivalót, pedig az a fejlécben úgyis ott van. | A H1 lett a domináns elem (54 px), a cégnév kikerült a hero-ból. |
| A hero-ból csak két általános gomb visz tovább. | „Gyors indítás" sáv: négy szolgáltatás-chip, amivel egyből a megfelelő helyre lehet ugrani. |
| A szolgáltatás szekció 1533 px magas: négy nagy fotós kártya, kártyánként ~380 px fotóval. Görgetni kell ahhoz, hogy egyáltalán kiderüljön, hány szolgáltatás van. | Négy tömör kártya egy sorban, ikonnal és a részfeladatok listájával – kb. 600 px. A látogató egy képernyőn látja a teljes kínálatot. |
| A folyamat szekcióban dupla a számozás: nagy „01" fölött, majd „1. Válassza ki a szolgáltatásokat" a címben. | Egyszeri számozás; a címekből kikerült a sorszám. |
| A referencia-kártyák eltérő magasságúak, a „Projekt megtekintése" link máshol áll kártyánként. | Egységes kártyaszerkezet, alul rögzített linkkel, kategória-címkékkel. |
| A hero és a szolgáltatások között nincs semmi, ami elmondaná, mitől más ez a folyamat. | Keskeny sáv három rövid ténnyel (több szolgáltatás egy kérésben / dokumentum-feltöltés / portálos követés). |

## Szolgáltatás lista oldal

| Megfigyelés | Változtatás |
|---|---|
| A négy terület egyetlen hosszú listában követi egymást, navigáció nélkül; a Közmű-koordinációig sokat kell görgetni. | Területenként külön panel, fent ugróhivatkozásokkal. |
| A bepipálás semmilyen visszajelzést nem ad: a fejléc számlálója 0 marad, a kosár üres, amíg a lap alján lévő gombot meg nem nyomják. | Ragadós összegző panel a jobb oldalon; a kattintás azonnal beteszi a tételt, a számláló élőben frissül. |
| A „Részletek" gombok a szekció mellett vannak, de nem derül ki, hova visznek. | A területfejlécben, ikonnal és leírással együtt – az aloldalra visznek. |
| Ha valaki nem tudja, mit válasszon, nincs kiút. | Alul „Nem találja, amire szüksége van?" kártya kapcsolat gombbal. |

## Ajánlatkérő folyamat

| Megfigyelés | Változtatás |
|---|---|
| A `/szolgaltatasok/` oldalon bepipáltam egy tételt, majd átmentem `/ajanlatkeres/`-re: „A kosár üres", és a fejléc számlálója végig 0 maradt. A kiválasztás csak a lap alján lévő „Hozzáadás és továbblépés" gombbal kerül a kosárba. | A kattintás azonnal beteszi a tételt; a jobb oldali összegző és a fejléc számlálója élőben frissül. |
| A kiválasztás egyetlen hosszú, görgetős oldal, visszalépési lehetőség nélkül. | Négy világos lépés, kattintható lépéssávval, előre-hátra mozgással. |
| A tételspecifikus mezők (pl. „Becsült vízigény (m³/nap)") a részletes nézetben egy hosszú listában jelennek meg. | A 2. lépésben a közös projektadatok után tételenként külön blokkban vannak. |
| A beküldés után nem derül ki, mi következik. | A visszaigazolás megadja az azonosítót és három lépésben, hogy mi történik ezután. |

## Ügyfélportál

| Megfigyelés | Változtatás |
|---|---|
| A négy statisztika-csempe (2 aktív / 1 új ajánlat / 0 terv / 1 fizetésre vár) semmire nem kattintható, és nem derül ki belőlük, mit kell tenni. | A lista fölé teendő-sáv került: melyik kérésnél kell most lépni, és mit. |
| Ugyanezek a csempék statikus számok maradtak volna. | A csempék most szűrők: rákattintva a lista alattuk szűrődik, és ugyanazt a négy állapotot mutatják, mint a fülek. A negyedik csempe „Fizetésre vár" maradt (a rendszerben van Stripe-alapú fizetés), a „Lezárt" helyett. |
| A táblázat a státuszt mutatja („Ajánlat elkészült"), de nem azt, hogy ez az ügyfélnek mit jelent. | A státusz alatt ott a következő lépés is. |
| Nincs szűrés, minden kérés egy listában. | Fülek: Összes / Folyamatban / Ajánlat elérhető / Lezárt. |
| A dokumentumok csak a kérés részletes nézetében érhetők el. | Külön „Dokumentumaim" panel a portál főoldalán. |
| A részletes nézetben az adatok, a dokumentumok és a feltöltő űrlap egy hosszú oszlopban követik egymást. | Két oszlop: balra a kérés tartalma, jobbra az ajánlat, a tervek és a státuszelőzmény. |

## Szolgáltatás aloldal (új)

A jelenlegi oldalon a „Részletek" gombok a `/szolgaltatasok/` szekcióihoz visznek, de nincs
olyan oldal, ami elmondaná, mit tartalmaz egy szolgáltatás, mikor van rá szükség, és mit kell
hozzá összekészíteni. Ez a lépés hiányzik a főoldal és az ajánlatkérés között. Az új sablon
ezt tölti be, és mind a négy területre használható.

## Referenciák (lista)

| Megfigyelés | Változtatás |
|---|---|
| A jelenlegi lista szűrés nélküli felsorolás; aki csak lakossági vagy csak közmű-projektre kíváncsi, mindent végignéz. | Szűrés típus (lakossági / üzleti) és szolgáltatás szerint, élő darabszámmal és értelmes üres állapottal. |
| A kártyák a főoldalon és a listaoldalon eltérően viselkednek. | Ugyanaz a `.ref` komponens mindkét helyen. |

## Referencia projekt oldal

| Megfigyelés | Változtatás |
|---|---|
| A projektoldalról nincs út a megrendelésig – a „Projekt megtekintése" zsákutca. | Jobb oldalon „Ehhez kapcsolódó szolgáltatások" panel, ahonnan egy kattintással ajánlatkérésbe kerülnek a tételek. |
| Nem derül ki, mi volt a feladat és mi lett az eredmény. | A feladat / Amit csináltunk / Eredmény / Képek szerkezet, tételes „amit csináltunk" táblával. |

## Reszponzív és mobil

Nincs külön mobil verzió: minden oldal ugyanabból a markupból és CSS-ből renderelődik,
media query-kkel. Három töréspont: 1100 (kis desktop), 900 (tablet), 640 (telefon).

| Mit old meg | Hogyan |
|---|---|
| A főmenü nem fér ki kis kijelzőn. | 900 px alatt hamburger gomb lép a helyére. |
| A portál táblázata mobilon olvashatatlanul összenyomódna. | 900 px alatt a sorok kártyákká alakulnak, teljes szélességű „Részletek" gombbal. |
| A kosár-összegző mobilon a tartalom alá kerülne, és a „Tovább" eltűnne a görgetésben. | Ragadós alsó cselekvési sáv a kiválasztott tételek számával. |
| Az apró kattintható elemek ujjal nem célozhatók. | Minden érintőfelület legalább 44 px, a fő gombok 48 px. |
| A hosszú gombfeliratok kilógnának 390 px-en. | 640 px alatt a gombok tördelhetők, teljes szélességűek, egymás alatt. |
| A szűrő fülek nem férnek ki egy sorba. | Vízszintesen görgethető fülsáv. |

Mind a 21 nézetet lerendereltem 390, 768 és 1440 px szélességen: sehol nincs vízszintes
túlcsordulás.


## Egységesítés (minden oldal)

A tervek egy közös elemkészletre épülnek, hogy az oldalak ne essenek szét:

- azonos felső sáv, fejléc és lábléc minden oldalon
- azonos sötétkék oldalfejléc (`.pagehead`) morzsamenüvel és 40 px-es címmel a
  Szolgáltatások, Ajánlatkérés, Ügyfélportál és Szolgáltatás aloldal tetején
- a kiválasztó felület a Szolgáltatások oldalon és az ajánlatkérő 1. lépésében
  ugyanaz a komponens
- azonos panel-, gomb-, jelölő- és összegző elemek
- azonos térközök: tartalmi szekció 72 px, alkalmazás-nézet 40 px felül / 64 px alul


## Nyelvválasztó (HU / RO / EN)

| Megfigyelés | Változtatás |
|---|---|
| A háromnyelvű váltó formázatlan felsorolásként jelenik meg a fejléc fölött, a menüsávba lógva. | Kompakt hármas kapcsoló a felső sáv jobb szélén, az aktív nyelv sötétkék kitöltéssel; ugyanez megismételve a láblécben. |
| Mobilon nem volt helye. | A felső sáv mobilon egyetlen sorrá egyszerűsödik: telefonszám balra, nyelvváltó jobbra, 44 px magas gombokkal; az e-mail és a nyitvatartás elrejtve. |
