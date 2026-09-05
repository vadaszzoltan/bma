# BMA Engineering – oldalfejlesztés: implementációs csomag

Ez a csomag a Claude Design vásznon elkészült tervek **kódra váltásához** készült.
Nem kész WordPress plugin/téma, hanem **referencia-markup + kész CSS + pontos utasítások**,
amit Cursorban rá lehet illeszteni a meglévő kódra.

A tervek: *BMA Engineering – oldalfejlesztés* vászon (Főoldal / Ajánlatkérés / Ügyfélportál / Szolgáltatás aloldal).

---

## 1. Mit tartalmaz

```
css/bma-v2.css              Kész, teljes CSS az összes új szekcióhoz és képernyőhöz
css/mobil-referencia.css    A mobil makett (390 px) stílusai – értékek átvételéhez, nem drop-in
markup/01-fooldal.html               Főoldal szekciók markupja
markup/02-szolgaltatasok.html        Szolgáltatás lista oldal (/szolgaltatasok/)
markup/03-ajanlatkeres.html          Ajánlatkérő varázsló 4 lépésének markupja
markup/04-ugyfelportal.html          Ügyfélportál lista + részletes nézet markupja
markup/05-szolgaltatas-aloldal.html  Szolgáltatás aloldal sablon (minta: Tervezés)
markup/06-fooldal-mobil.html         Mobil főoldal makett
PROMPT.md                   Bemásolható indító prompt Cursorhoz
VALTOZASOK.md               Mi változott és miért – szekciónként
```

## 2. Amire a kód épül (a jelenlegi oldalról leolvasva)

| Mi | Hol |
|---|---|
| Gyerektéma | `wp-content/themes/bma-engineering-child/` (szülő: `ollie`) |
| Téma CSS | `wp-content/themes/bma-engineering-child/assets/css/theme.css` |
| Ajánlatkérés plugin | `wp-content/plugins/bma-ajanlatkeres/` |
| Plugin CSS (itt vannak a `:root` tokenek) | `wp-content/plugins/bma-ajanlatkeres/assets/css/public.css` |
| Oldalak | `/`, `/szolgaltatasok/`, `/ajanlatkeres/`, `/ugyfelportal/` (+ `?request=…`), `/referenciak/`, `/kapcsolat/` |

**Design tokenek – ezek NEM változnak:**

```
--bma-primary  #0e2356      --bma-accent  #ee7643      --bma-bg      #f7f8fa
--bma-text     #0e2356      --bma-muted   #6b7280      --bma-border  #e6e9ef
--bma-tint     #fff1eb      --bma-soft    #eef1f7      --bma-radius  0px
--bma-font-heading "Manrope"      --bma-font-body "Source Sans 3"
--bma-container 1200px
--bma-primary-dark #0a1a40   --bma-success #1e8a4c
--bma-shadow 0 8px 24px rgba(14,35,86,.08)   --bma-shadow-sm 0 2px 8px rgba(14,35,86,.06)

Egy új token kell hozzá (a bma-v2.css definiálja): `--bma-accent-dark: #d45e2c`
(gomb hover és a világos háttéren lévő narancs szöveg).
```

A terv végig ezekre épül. Ha valami mégis eltér, a token az igazság, nem a markup.

**Egységes elemkészlet.** Minden aloldal ugyanazokat a komponenseket használja – ezek a
`bma-v2.css` elején, a „shared" blokkokban vannak, és nem oldalspecifikusak:

| Komponens | Osztály | Hol |
|---|---|---|
| Felső sáv / fejléc / lábléc | `.topbar`, `.siteheader`, `.footer` | minden oldal |
| Sötétkék oldalfejléc | `.pagehead` (+ `--split` / `--row`) | szolgáltatások, ajánlatkérés, portál, aloldal |
| Morzsamenü | `.crumb` | minden aloldal |
| Kártya-panel | `.panel`, `.panel-head`, `.panel-body` | szolgáltatások, ajánlatkérés, portál |
| Kiválasztható tétel | `.task`, `.box`, `.task-when` | szolgáltatások, ajánlatkérés |
| Kosár-összegző | `.summary`, `.sum-item`, `.sum-foot` | szolgáltatások, ajánlatkérés |
| Gombok | `.btn` + `--primary` / `--outline` / `--ghost-light` / `--sm` | minden oldal |
| Folyamatlépések | `.process`, `.step` | főoldal, szolgáltatás aloldal |
| Sötét háttéren lévő eyebrow | `.eyebrow--light` | minden sötét sáv |
| Alkalmazás-nézet háttér | `.appbody` (40 px felül / 64 px alul) | szolgáltatások, ajánlatkérés, portál |
| Tartalmi szekció | `.bma-section` (72 px) | főoldal, szolgáltatás aloldal |

Ha új oldal készül, ezekből kell összerakni – ne szülessen új variáns ugyanarra a dologra.

## 3. Lépések

### 3.1 CSS bekötése

Másold: `css/bma-v2.css` → `wp-content/themes/bma-engineering-child/assets/css/bma-v2.css`

A gyerektéma `functions.php`-jában, a meglévő `theme.css` enqueue **után**:

```php
wp_enqueue_style(
    'bma-v2',
    get_stylesheet_directory_uri() . '/assets/css/bma-v2.css',
    array( 'bma-child-theme' ), // a meglévő theme.css handle-je
    filemtime( get_stylesheet_directory() . '/assets/css/bma-v2.css' )
);
```

A handle nevét a meglévő `wp_enqueue_style` hívásból vedd át – ne találd ki.

### 3.2 Főoldal

Forrás: `markup/01-fooldal.html`

A jelenlegi főoldal szekció-osztályai megmaradnak, csak a belső markup cserélődik:

| Jelenlegi szekció | Új markup a fájlban |
|---|---|
| `.bma-hero` | `<section class="hero">` blokk |
| — (új) | `.valuestrip` sáv a hero alatt |
| `.bma-home-services` | `.svc-grid` négy `.svc-card`-dal |
| `.bma-home-multi` | `.multi` (bal szöveg + jobb `.stackcard`) |
| `.bma-home-process` | `.process` négy `.step`-pel |
| `.bma-home-benefits` | `.benefits` |
| `.bma-home-references` | `.refs` |
| `.bma-home-about` | `.about` + `.factbox` |
| `.bma-home-closing` | `.closing` |

Fontos:
- A szekciók külső `.bma-section` / `.bma-container` burkolóit **hagyd meg**, ha a template már így épül fel.
- A szolgáltatás-kártyák tartalma (név, leírás, részfeladatok) **adatból** jöjjön, ahogy most is – ne égesd be. A markupban lévő szöveg csak minta.
- A referencia-kártyák `.ref-img` blokkja most gradiens helyőrző. Ide a meglévő kiemelt kép kerül: `<img>` vagy `background-image`, `object-fit: cover`, magasság 172 px.
- A hero háttere jelenleg mérnöki vonalrajz (inline SVG). Ha marad a fotó, tedd a fotót a `.hero`-ra háttérként, és hagyd a `<svg class="hero-blueprint">`-et fölötte – együtt is jól működnek.

### 3.3 Szolgáltatás lista oldal (`/szolgaltatasok/`)

Forrás: `markup/02-szolgaltatasok.html`

A jelenlegi oldal egyetlen hosszú lista: négy szekció, mindegyikben checkboxos kártyák,
és a lap alján egy „Hozzáadás és továbblépés" gomb. Kipróbáltam: a bepipálás után a fejléc
`AJÁNLAT` számlálója 0 maradt, és az `/ajanlatkeres/` oldal „A kosár üres" üzenetet adott.

Az új felépítés:

- Sötétkék oldalfejléc morzsamenüvel és egy „Hogyan működik?" dobozzal (`.sidecard`).
- Fent ugróhivatkozások (`.jump`) a négy területhez.
- Területenként egy panel: ikon + cím + leírás + „Részletek" gomb az aloldalra,
  alatta kétoszlopos tételrács. Az „Ezt válassza, ha:" szövegek megmaradnak (`.task-when`).
- Jobb oldalon **ragadós összegző** (`.summary`): a kattintás azonnal beteszi a tételt,
  a számláló és a fejléc `.cart-count` egyszerre frissül. Üres kosárnál a továbblépés
  `.is-disabled`, és a gomb alatt ott az indoklás.
- Alul „Nem találja, amire szüksége van?" kártya kapcsolat gombbal.

Fontos: ez a kiválasztó felület **ugyanaz a komponens**, mint az ajánlatkérő varázsló 1. lépése.
Egy sablonrészletként (template part) valósítsd meg, és mindkét helyen azt használd – ne
duplikáld a markupot.

### 3.4 Ajánlatkérő folyamat

Forrás: `markup/03-ajanlatkeres.html`

Ez a legnagyobb funkcionális változás. A jelenlegi működés:
`/szolgaltatasok/` hosszú lista checkboxokkal → külön „Hozzáadás és továbblépés" gomb → `/ajanlatkeres/`.
A fejléc `AJÁNLAT 0` számlálója addig 0 marad, amíg nem nyomtak gombot.

A terv szerinti működés:

1. **1. lépés – kiválasztás.** Kattintásra azonnal be/ki kapcsol a tétel (`.task.is-sel`),
   és **azonnal frissül** a jobb oldali ragadós összegző (`.summary`) meg a fejléc számlálója.
   Nincs külön „hozzáadás és továbblépés" gomb a lista alján; a „Tovább a projekt adataihoz"
   viszi a következő lépésre. (A szolgáltatás aloldalakon lévő tételenkénti „Hozzáadás" gombok
   ugyanezt a kosarat töltik, szintén azonnali visszajelzéssel.)
2. **2. lépés – projekt adatai.** Fent a minden tételre vonatkozó közös mezők,
   alatta tételenként egy-egy blokk (`.itemform`) a tételspecifikus mezővel
   (pl. „Becsült vízigény (m³/nap)").
3. **3. lépés – ellenőrzés és kapcsolattartás.** Tételek listája törléssel, kapcsolati mezők, hozzájárulás.
4. **4. lépés – visszaigazolás.** Azonosító + „mi történik ezután" három lépésben.

Implementációs javaslat (a plugin jelenlegi felépítéséhez igazítva):
- A kosár maradhat a jelenlegi session/tranziens tárolásban; a kiválasztás legyen
  **AJAX** (`wp_ajax_` / `wp_ajax_nopriv_` + nonce), ami visszaadja az aktuális kosarat,
  és a JS frissíti a `.summary`-t meg a `.cart-count`-ot.
- A lépéssáv (`.stepchip`) állapotai: `is-done` (kész), `is-now` (aktuális), üres (még nem elért).
- Üres kosárral a továbblépés le van tiltva (`.btn.is-disabled`) és a gomb alatt magyarázat áll.
- A fejléc `.cart-count` értékét **minden oldalon** ugyanaz az AJAX válasz frissítse
  (a makettben csak az ajánlatkérő artboardon él, a többin statikus 0).
- Escape-elj mindent `esc_html()` / `esc_attr()` hívással, a szövegeket `__()`-be tedd
  (a jelenlegi text domain szerint).

A markupban a `{{valami}}` jelölések dinamikus értékek – ezeket PHP echo-ra kell cserélni.

### 3.5 Ügyfélportál

Forrás: `markup/04-ugyfelportal.html`

Két nézet egy fájlban: lista (`isList`) és részletes (`isDetail`) – a jelenlegi
`/ugyfelportal/` és `/ugyfelportal/?request=…` felosztásnak felel meg.

Változások:
- Legfelül **teendő-sáv** (`.todo`): mi az, amire az ügyfélnek most lépnie kell.
- A statisztika-csempék (`.stat`) megmaradnak, csak keretes kártya formában.
- A táblázat sorai (`.reqrow`) a státusz mellett a **következő lépést** is mutatják.
- Szűrő fülek (`.tab`): Összes / Folyamatban / Ajánlat elérhető / Lezárt – szerver oldali
  szűrés query paraméterrel vagy JS-es sorszűrés, ahogy egyszerűbb.
- „Dokumentumaim" panel: az összes feltöltött és kapott fájl kérésektől függetlenül.
- Részletes nézet: bal oldalon tételek + projektadatok + dokumentumok, jobb oldalon
  az ajánlat kártya (`.offer`), az elkészült tervek és a státuszelőzmény (`.tl`).

### 3.6 Szolgáltatás aloldal

Forrás: `markup/05-szolgaltatas-aloldal.html`

A `/szolgaltatas/tervezes/` oldal már létezik – ez annak az átdolgozása, ugyanazzal a
tartalommal, de a többi oldallal egységes felépítésben. A sablon mind a négy területre
használható (Tervezés / Engedélyeztetés / Műszaki dokumentáció / Közmű-koordináció).

Külön hiba, amit érdemes itt rendbe tenni: a jelenlegi oldalon a hu/ro/en nyelvváltó
formázatlan felsorolásként jelenik meg a fejléc fölött.

Szerkezet: hero + „Röviden" doboz → részfeladatok kártyái („Ezt válassza, ha…") →
„Mire lesz szükség?" (kötelező / ha van / később is pótolható) → folyamat → GYIK → CTA.

WordPress oldalról: egyedi `single-{post_type}.php` vagy `page-szolgaltatas.php` sablon,
a részfeladatok ugyanabból az adatforrásból, ahonnan a `/szolgaltatasok/` oldal dolgozik.

## 4. Szabályok, amiket kérlek tartsatok be

- **Ne írd át a `:root` tokeneket** a plugin `public.css`-ében. Az új CSS csak hozzáad.
- **Ne nyúlj a szülő témához** (`ollie`). Minden módosítás a gyerektémába vagy a pluginba megy.
- A meglévő PHP logikát (lekérdezések, kosár, jogosultság, nonce) **tartsd meg** – ez a csomag
  a megjelenésről és a folyamat sorrendjéről szól, nem az üzleti logikáról.
- Minden felhasználói szöveg fordítható és escape-elt legyen.
- Az ikonok inline SVG-k (stroke alapú, 20/24 px rács). Ne cseréld emojira.
- A sarokkerekítés 0 px (`--bma-radius: 0px`) – ez szándékos, ne adj hozzá `border-radius`-t.

### Akadálymentesség – ezt kérlek a kódban rendbe tenni

A makettben a legfontosabbak már valódi elemek (`<button>`, `<textarea>`), de a bekötésnél
ezek is kellenek:

- A tétel-választó valódi `<input type="checkbox">` legyen, `<label>`-lel összekötve
  (`id` / `for`), a `.task` csak a vizuális burkolat. Ugyanez a hozzájárulás-jelölőkre.
- Minden űrlapmezőnek legyen társított `<label for="…">`.
- A díszítő SVG-k kapjanak `aria-hidden="true"`-t, a jelentést hordozók `<title>`-t.
- A lépéssáv `aria-current="step"`-pel jelölje az aktuális lépést, a fülek `role="tab"` /
  `aria-selected` párral működjenek.
- **Kontraszt:** a `#ee7643` fehér háttéren 2,9:1 – ez a WCAG AA (4,5:1) alatt van.
  A nagy narancs gombfeliratokon és a nagy címeken elmegy, de a kis narancs szövegeknél
  (linkek, `.eyebrow`, „Letöltés", „Projekt megtekintése →") érdemes egy sötétebb változatot
  bevezetni (kb. `#b5461a` felel meg 4,5:1-nek fehéren). Ez márka-döntés, ezért nem írtam át
  a tervben – kérlek egyeztessétek, és ha rábólintotok, egy tokennel megoldható.

## 5. Amit nektek kell kitölteni

A markupban `[SZÖGLETES ZÁRÓJELBEN]` szerepel minden olyan adat, amit nem tudtam a jelenlegi
oldalról kiolvasni, és nem akartam kitalálni:

- `[X] munkanapon belül` – tényleges válaszidő az ajánlatra
- `[X–Y] hét` – tipikus átfutási idő szolgáltatásonként
- `[X] MB` – feltölthető fájlméret korlát
- `[MAGYAR / ROMÁN]` – kiszolgálási nyelvek
- `[X] napig érvényes`, `[ÖSSZEG] EUR` – ajánlat érvényessége és összege a portálon
- `[HELYRAJZI SZÁM]`, `[X] m²`, `[X] m³/nap` – a portál mintaadatai
- `[ÜGYFÉL NEVE]`, `[email cím]` – a portál üdvözlő sora (bejelentkezett felhasználóból)
- `[LOGÓ]` – a fejléc, lábléc és mobil fejléc logóhelye
- `[Projekt fotó]` – a referencia-kártyák kiemelt képe
- GYIK válaszszövegek a szolgáltatás aloldalon
- Referencia projektek valós fotói

## 6. Amit ez a csomag NEM tartalmaz

- Nem néztem bele a PHP forrásba (a `localhost:8081` a ti gépeteken fut, csak a böngészőn
  keresztül láttam az oldalt). A template-fájlok neveit és a hook-okat Cursornak a helyszínen
  kell megtalálnia.
- Nincs benne adatbázis-migráció, e-mail sablon, sem fizetési folyamat.
- A mobil nézet makett formájában van meg; a töréspontokat (`@media`) a bekötéskor kell megírni.
