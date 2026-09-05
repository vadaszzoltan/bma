# BMA Engineering – oldalfejlesztés: implementációs csomag

> **FIGYELEM:** olvasd el először a `FELDERITES-UTAN.md`-t. Az a fájl a projekt tényleges
> felépítésének megismerése után készült, és felülírja az itteni feltételezések egy részét
> (a markup a pluginba megy, nem a témába; az enqueue handle `bma-theme-layout`; a
> referencia-szűrő metára épül, nem taxonómiára; a fizetés része a rendszernek).

Ez a csomag a Claude Design vásznon elkészült tervek **kódra váltásához** készült.
Nem kész WordPress plugin/téma, hanem **referencia-markup + kész, reszponzív CSS +
pontos utasítások**, amit Cursorban rá lehet illeszteni a meglévő kódra.

A tervek: *BMA Engineering – oldalfejlesztés* vászon. Hét oldal, mindegyik három
képernyőméretben (desktop 1440 / tablet 768 / mobil 390).

---

## 1. Mit tartalmaz

**Csomag verzió: v2 · 2026-09-05.** Minden új zip a TELJES `bma-redesign-cursor/` mappát
felülírja – ne fájlonként másolj be belőle.

```
FELDERITES-UTAN.md                    KORREKCIÓK a felderítés után – ELŐSZÖR EZT OLVASD
README.md                             ez a fájl
SZOVEGVALTOZASOK.md                   MINDEN szövegeltérés az eredetihez képest
VALTOZASOK.md                         Mi változott és miért – oldalanként
PROMPT.md                             Bemásolható Cursor-prompt, lépésenként
css/bma-v2.css                        Teljes, reszponzív CSS minden oldalhoz
markup/01-fooldal.html                Főoldal
markup/02-szolgaltatasok.html         Szolgáltatás lista (/szolgaltatasok/)
markup/03-ajanlatkeres.html           Ajánlatkérő varázsló (4 lépés)
markup/04-ugyfelportal.html           Ügyfélportál (lista + részletes nézet)
markup/05-referenciak.html            Referenciák lista, szűrőkkel
markup/06-referencia-projekt.html     Referencia projekt oldal
markup/07-szolgaltatas-aloldal.html   Szolgáltatás aloldal (minta: Tervezés)
```

Összesen **11 fájl**: 5 dokumentum, 1 CSS, 7 markup (a `markup/` mappában). Ha kevesebb
van a kicsomagolás után, a csomag hiányos – állj meg.

**Egy markup, három nézet.** Nincs külön mobil oldal és nincs eszközfelismerés:
minden oldal ugyanabból a markupból és CSS-ből renderelődik, a méretek media
query-kkel változnak. A vásznon látható mobil és tablet nézetek pontosan ezt a
CSS-t futtatják 390 és 768 px szélességen.

## 2. Amire a kód épül (a jelenlegi oldalról leolvasva)

| Mi | Hol |
|---|---|
| Gyerektéma | `wp-content/themes/bma-engineering-child/` (szülő: `ollie`) |
| Téma CSS | `wp-content/themes/bma-engineering-child/assets/css/theme.css` |
| Ajánlatkérés plugin | `wp-content/plugins/bma-ajanlatkeres/` |
| Plugin CSS (itt vannak a `:root` tokenek) | `wp-content/plugins/bma-ajanlatkeres/assets/css/public.css` |
| Oldalak | `/`, `/szolgaltatasok/`, `/szolgaltatas/{slug}/`, `/ajanlatkeres/`, `/ugyfelportal/` (+ `?request=…`), `/referenciak/`, `/kapcsolat/` |

**Design tokenek – ezek NEM változnak:**

```
--bma-primary  #0e2356      --bma-accent  #ee7643      --bma-bg      #f7f8fa
--bma-text     #0e2356      --bma-muted   #6b7280      --bma-border  #e6e9ef
--bma-tint     #fff1eb      --bma-soft    #eef1f7      --bma-radius  0px
--bma-primary-dark #0a1a40  --bma-success #1e8a4c
--bma-font-heading "Manrope"      --bma-font-body "Source Sans 3"
--bma-container 1200px
--bma-shadow 0 8px 24px rgba(14,35,86,.08)
```

Egy új token kell hozzá (a `bma-v2.css` definiálja): `--bma-accent-dark: #d45e2c`
(gomb hover és világos háttéren lévő narancs szöveg).

**Egységes elemkészlet.** Minden oldal ugyanazokat a komponenseket használja – ezek a
`bma-v2.css` elején, a „shared" blokkokban vannak:

| Komponens | Osztály | Hol |
|---|---|---|
| Felső sáv / fejléc / lábléc | `.topbar`, `.siteheader`, `.footer` | minden oldal |
| Sötétkék oldalfejléc | `.pagehead` (+ `--split` / `--row`) | minden aloldal |
| Morzsamenü | `.crumb` | minden aloldal |
| Kártya-panel | `.panel`, `.panel-head`, `.panel-body` | listák, űrlapok, portál |
| Kiválasztható tétel | `.task`, `.box`, `.task-when` | szolgáltatások, ajánlatkérés |
| Kosár-összegző | `.summary`, `.sum-item`, `.sum-foot` | szolgáltatások, ajánlatkérés |
| Referencia-kártya | `.refs`, `.ref`, `.ref-tag` | főoldal, referenciák |
| Gombok | `.btn` + `--primary` / `--outline` / `--ghost-light` / `--sm` | minden oldal |
| Folyamatlépések | `.process`, `.step` | főoldal, szolgáltatás aloldal |
| Alkalmazás-nézet háttér | `.appbody` (40 px felül / 64 px alul) | listák, űrlapok, portál |
| Tartalmi szekció | `.bma-section` (72 px) | főoldal, aloldalak |
| Mobil hamburger | `.burger` | minden oldal (900 px alatt látszik) |
| Nyelvválasztó | `.langswitch`, `.lang` (+ `--light` a láblécben) | minden oldal |
| Mobil ragadós sáv | `.mobilebar` | főoldal, szolgáltatások, ajánlatkérés, referenciák, aloldalak |

Ha új oldal készül, ezekből kell összerakni – ne szülessen új variáns ugyanarra.

## 2.b Melyik markup melyik PHP fájlba megy

| Terv-fájl | Cél PHP (plugin) | Shortcode / CPT |
|---|---|---|
| `01-fooldal.html` | `templates/frontend/home-*.php` (8 fájl) | `[bma_home_*]` |
| `02-szolgaltatasok.html` | `templates/frontend/services-list.php` + `partials/select-card.php`, `partials/selection-bar.php` | `[bma_services_list]` |
| `03-ajanlatkeres.html` | `templates/frontend/quote-cart.php`, `quote-form.php` | `[bma_quote_cart]`, `[bma_quote_form]` |
| `04-ugyfelportal.html` | `templates/frontend/client-portal.php` | `[bma_client_portal]` |
| `05-referenciak.html` | `templates/frontend/references-list.php` | `[bma_references_list]` |
| `06-referencia-projekt.html` | `templates/single-bma_reference.php` | CPT `bma_reference` → `/referencia/{slug}/` |
| `07-szolgaltatas-aloldal.html` | `templates/single-bma_service.php` | CPT `bma_service` → `/szolgaltatas/{slug}/` |

Minden markup fájl elején ott van ugyanez kommentben, a használt komponensekkel együtt.

## 2.c Token-szótár

A markup `{{...}}` jelölései a prototípus adatkötései. Így fordítsd PHP-re:

| Token | Jelentése | Forrás |
|---|---|---|
| `{{t.name}}`, `{{t.hint}}`, `{{t.when}}` | egy részfeladat neve / rövid leírása / „Ezt válassza, ha” szövege | `bma_service` gyerek-tétel mezői |
| `{{t.cls}}` | `is-sel`, ha a tétel a kosárban van | `BMA_AJ_Cart` |
| `{{c.name}}`, `{{c.area}}` | kosárban lévő tétel neve és szolgáltatási területe | kosár tartalma |
| `{{c.extraLabel}}`, `{{c.extraPh}}` | tételspecifikus mező címkéje és példa-értéke | tétel meződefiníciója |
| `{{count}}` | kosárban lévő tételek száma | `BMA_AJ_Cart` |
| `{{stepTitle}}`, `{{stepIntro}}` | az aktuális lépés címe és bevezetője | statikus, lépésenként |
| `{{c1}}…{{c4}}` | lépés-állapot: `is-done` / `is-now` / üres | aktuális lépés |
| `{{nextCls}}`, `{{ctaCls}}` | `is-disabled`, ha üres a kosár | `BMA_AJ_Cart` |
| `{{ctaNote}}` | a gomb alatti magyarázat (üres / nem üres kosár) | statikus, két változat |
| `{{ref}}` | beküldött ajánlatkérés azonosítója | `bma_submit_quote_request` válasza |
| `{{r.id}}`, `{{r.date}}`, `{{r.svc}}`, `{{r.status}}`, `{{r.next}}`, `{{r.badgeCls}}` | portál: egy kérés sorának adatai | quote request rekord |
| `{{curId}}`, `{{curDate}}`, `{{curStatus}}`, `{{curItems}}`, `{{curPrice}}`, `{{curOfferNo}}`, `{{curValid}}`, `{{curTimeline}}` | portál részletes nézet | quote request + ajánlat |
| `{{tabs}}`, `{{stats}}`, `{{rows}}`, `{{noRows}}` | portál szűrők, csempék, sorok, üres állapot | lekérdezés eredménye |
| `{{types}}`, `{{svcs}}`, `{{items}}`, `{{isEmpty}}`, `{{countLabel}}` | referencia-szűrő és -lista | `_bma_project_type`, `_bma_related_services` |
| `{{r.kicker}}`, `{{r.title}}`, `{{r.lead}}`, `{{r.tags}}` | referencia-kártya | `bma_reference` mezői |

`<sc-if value="{{x}}">` = PHP `if`, `<sc-for list="{{xs}}" as="y">` = `foreach`.
A `03-ajanlatkeres.html`-ben `<!-- step:0N -->` kommentek mondják meg, melyik blokk melyik
template-be tartozik.

## 3. Reszponzív viselkedés

Három töréspont. A media query-k a `bma-v2.css` **végén** vannak – új szabályt
elé kell tenni, különben felülírja őket.

### 1100 px alatt (kis desktop / fekvő tablet)
- keskenyebb belső margók (24 → 20 px)
- a 4 oszlopos rácsok (szolgáltatások, referenciák) 2 oszlopra váltanak
- a hero cím 54 → 44 px

### 900 px alatt (tablet)
- a főmenü eltűnik, helyette **hamburger gomb** (`.burger`) – a mobil menü megnyitása
  a ti JS-etek dolga lesz, a gomb és a helye kész
- az oldalfejléc kétoszlopos változata (`--split`, `--row`) egy oszlopba rendeződik
- a tartalom + kosár elrendezés (`.wiz-grid`) egy oszlop lesz, az összegző már nem ragadós
- a portál **táblázata kártyákká alakul**: a fejlécsor eltűnik, minden sor önálló blokk
  lesz, teljes szélességű „Részletek" gombbal
- a folyamatlépések 4 → 2 oszlop, az űrlapmezők 1 oszlop

### 640 px alatt (telefon)
- alapméret 18 → 16 px, `.h2` 36 → 26 px, oldalcím 40 → 28 px
- minden rács 1 oszlop
- a gombok teljes szélességűek, egymás alatt (`.btnrow`), és tördelhetők
- **ragadós alsó cselekvési sáv** (`.mobilebar`) a fő oldalakon: a főoldalon és az
  aloldalakon „Ajánlatkérés", a szolgáltatásoknál és az ajánlatkérőnél a kiválasztott
  tételek számával és a „Tovább" gombbal
- a fülek (`.tabs`) vízszintesen görgethetők
- a statisztika-csempék 2×2-be rendeződnek
- a felső sávból elrejtjük a másodlagos adatokat (e-mail, nyitvatartás)

### Érintőfelületek
Minden kattintható elem legalább **44 px** magas mobilon (`.task`, `.fchip`, `.jump a`,
`.quick-chip`, `.tab`), a fő gombok **48 px**. Ezt kérlek ne csökkentsd a bekötéskor.

### Amit ellenőriztem
Mind a 21 nézetet lerendereltem 390, 768 és 1440 px szélességen, és megnéztem, hogy
sehol nincs vízszintes túlcsordulás (`scrollWidth == clientWidth`). Az egyetlen szándékos
kivétel a szűrő fülek sávja, ami vízszintesen görgethető.

## 4. Lépések

### 4.1 CSS bekötése

Másold: `css/bma-v2.css` → `wp-content/themes/bma-engineering-child/assets/css/bma-v2.css`

A gyerektéma `functions.php`-jában, a meglévő `theme.css` enqueue **után**:

```php
wp_enqueue_style(
    'bma-v2',
    get_stylesheet_directory_uri() . '/assets/css/bma-v2.css',
    array( 'bma-theme-layout' ), // a meglévő theme.css handle-je
    filemtime( get_stylesheet_directory() . '/assets/css/bma-v2.css' )
);
```

A handle-ek: `theme.css` → `bma-theme-layout`, `style.css` → `bma-child-style`.

**A CSS két fájl**, és így is marad: `bma-v2.css` (ez a csomagból jön, minden frissítés
felülírja) és `bma-v2-integration.css` (téma-specifikus illesztések, a csomag nem nyúl
hozzá). Az integration fájl a `bma-v2` után, tőle függve töltődik. Részletek:
`FELDERITES-UTAN.md` 2. pont.

Ellenőrizd, hogy a `<head>`-ben ott van-e:
`<meta name="viewport" content="width=device-width, initial-scale=1">` – enélkül a
media query-k mobilon nem érvényesülnek.

### 4.2 Nyelvválasztó (HU / RO / EN)

Az oldal három nyelven érhető el, de a váltó jelenleg formázatlan felsorolásként jelenik meg
a fejléc fölött. A tervben egy kompakt, hármas kapcsoló került a **felső sáv jobb szélére**,
és ugyanez megismételve a **láblécben** is (`.langswitch--light`).

```html
<div class="langswitch" role="group" aria-label="Nyelv választása">
  <a href="/szolgaltatasok/"    class="lang is-on" lang="hu" hreflang="hu" aria-current="true">HU</a>
  <a href="/ro/servicii/"       class="lang"       lang="ro" hreflang="ro">RO</a>
  <a href="/en/services/"       class="lang"       lang="en" hreflang="en">EN</a>
</div>
```

Amire figyelni kell a bekötésnél:

- **A linkek az AKTUÁLIS oldal fordítására mutassanak**, ne a kezdőlapra. Polylang esetén
  `pll_the_languages( array( 'raw' => 1 ) )` és abból építsd fel a fenti markupot; WPML-nél
  `apply_filters( 'wpml_active_languages', null, 'skip_missing=0' )`.
- Ha egy oldalnak nincs fordítása, a link vagy a kezdőlapra mutasson, vagy a nyelv kapjon
  `.lang--off` állapotot (halvány, nem kattintható) – ne vezessen 404-re.
- Az aktív nyelv `aria-current="true"`, és `.is-on` osztályt kap (sötétkék kitöltés).
- Minden `<a>`-ra kerüljön `hreflang`, és a `<head>`-be `<link rel="alternate" hreflang="…">`
  a három nyelvre plusz `x-default` – enélkül a Google nem tudja összekötni a változatokat.
- A `<html lang="…">` attribútum kövesse az aktuális nyelvet.
- A számláló, a státuszok és a folyamat-szövegek is fordítandók: minden felhasználói szöveg
  `__()` / `_e()` hívásban legyen, a text domain a gyerektéma és a plugin sajátja.
- Mobilon a felső sáv egyetlen sorrá egyszerűsödik (telefonszám balra, nyelvváltó jobbra),
  a gombok 44 px magasak. A hamburger menübe is tegyétek be a nyelvváltót, amikor megírjátok.

### 4.3 Főoldal

Forrás: `markup/01-fooldal.html`

A jelenlegi szekció-osztályok megmaradnak, csak a belső markup cserélődik:
`.bma-hero` → `.hero`; új `.valuestrip` sáv; `.bma-home-services` → `.svc-grid`;
`.bma-home-multi` → `.multi`; `.bma-home-process` → `.process`;
`.bma-home-benefits` → `.benefits`; `.bma-home-references` → `.refs`;
`.bma-home-about` → `.about` + `.factbox`; `.bma-home-closing` → `.closing`.

- A dinamikus tartalom (szolgáltatások, referenciák) maradjon adatvezérelt.
- A `.ref-img` most gradiens helyőrző: ide a kiemelt kép kerül (`object-fit: cover`, 172 px).
- A hero háttere mérnöki vonalrajz (inline SVG). Ha marad a fotó, tedd a `.hero`-ra
  háttérként, és hagyd az SVG-t fölötte.

### 4.4 Szolgáltatás lista (`/szolgaltatasok/`)

Forrás: `markup/02-szolgaltatasok.html`

A jelenlegi oldal egyetlen hosszú lista, a lap alján „Hozzáadás és továbblépés" gombbal.
Kipróbáltam: a bepipálás után a fejléc `AJÁNLAT` számlálója 0 maradt, és az
`/ajanlatkeres/` „A kosár üres" üzenetet adott.

Az új felépítés: területenként panel (ikon + cím + leírás + „Részletek" gomb az aloldalra),
fent ugróhivatkozások, jobb oldalon ragadós kosár-összegző. A kattintás azonnal beteszi a
tételt, a számláló és a fejléc `.cart-count` egyszerre frissül. Üres kosárnál a
továbblépés `.is-disabled`, és a gomb alatt ott az indoklás.

Ez a kiválasztó felület **ugyanaz a komponens**, mint az ajánlatkérő 1. lépése – egy
sablonrészletként (template part) valósítsd meg, és mindkét helyen azt használd.

### 4.5 Ajánlatkérő folyamat

Forrás: `markup/03-ajanlatkeres.html`

Négy lépés: kiválasztás → projekt adatai → ellenőrzés és kapcsolattartás → visszaigazolás.

- A kiválasztás legyen **AJAX** (`wp_ajax_` / `wp_ajax_nopriv_` + nonce), ami visszaadja az
  aktuális kosarat; a JS frissíti a `.summary`-t, a `.cart-count`-ot és a `.mobilebar`-t.
- A lépéssáv (`.stepchip`) állapotai: `is-done`, `is-now`, üres.
- Üres kosárral a továbblépés le van tiltva (`.btn.is-disabled`).
- A tételenkénti mezők (pl. „Becsült vízigény") a saját tételük alatt vannak.
- A markupban a `{{valami}}` jelölések dinamikus értékek – PHP echo-ra cserélendők.

### 4.6 Ügyfélportál

Forrás: `markup/04-ugyfelportal.html`

Két nézet egy fájlban: lista (`isList`) és részletes (`isDetail`) – a jelenlegi
`/ugyfelportal/` és `/ugyfelportal/?request=…` felosztásnak felel meg.

Teendő-sáv legfelül; szűrhető statisztika-csempék és fülek (Összes / Folyamatban /
Ajánlat elérhető / Lezárt); a sorok a státusz mellett a következő lépést is mutatják;
„Dokumentumaim" panel; a részletes nézet két oszlopban (tételek és projektadatok, illetve
ajánlat, tervek, státuszelőzmény).

### 4.7 Referenciák lista (`/referenciak/`)

Forrás: `markup/05-referenciak.html`

- Szűrés **típus** (lakossági / üzleti) és **szolgáltatás** szerint. A szűrés lehet
  szerveroldali (query paraméter) vagy JS-es – a markup mindkettőhöz jó.
- A darabszám (`.filtercount`) a szűrés eredményét mutatja.
- Ha nincs találat, magyarázó üres állapot jelenik meg (`.ref-empty`), nem üres rács.
- A kártya ugyanaz a `.ref` komponens, mint a főoldalon – egy template part legyen.
- A projektek típusa és szolgáltatás-címkéi taxonómiából jöjjenek, hogy a szűrő
  automatikusan bővüljön új projekttel.

### 4.8 Referencia projekt oldal

Forrás: `markup/06-referencia-projekt.html`

Szerkezet: oldalfejléc a projekt adataival (`.sidecard`) → fő fotó (`.rd-hero`) →
A feladat / Amit csináltunk / Eredmény / Képek → jobb oldalon a **kapcsolódó
szolgáltatások**, ahonnan egy kattintással ajánlatkérésbe tehetők, alatta további projektek.

A lényeg a jobb oldali panel: ma a projektoldalról nincs út a megrendelésig. A „Hozzáadás"
gombok ugyanabba a kosárba tesznek, mint a szolgáltatás oldal.

### 4.9 Szolgáltatás aloldal (`/szolgaltatas/{slug}/`)

Forrás: `markup/07-szolgaltatas-aloldal.html`

A meglévő oldal átdolgozása, mind a négy területre használható sablonként.
Szerkezet: oldalfejléc + „Röviden" doboz → részfeladatok kártyái („Ezt válassza, ha…") →
„Mire lesz szükség?" → folyamat → GYIK → CTA.

Külön hiba, amit érdemes itt rendbe tenni: a jelenlegi oldalon a hu/ro/en nyelvváltó
formázatlan felsorolásként jelenik meg a fejléc fölött.

## 4.10 Szövegek

A markupban lévő szövegek egy része szó szerint a mostani oldalról származik, egy része
átfogalmazott, egy része új. **Mielőtt bármit lecserélsz, olvasd el a
`SZOVEGVALTOZASOK.md`-t** – ott tételesen szerepel, mi VÁLTOZATLAN, mi MÓDOSÍTOTT, mi ÚJ,
és mi HELYŐRZŐ. Amit az a fájl nem sorol fel, az változatlan: azt a meglévő stringből vagy
adatbázis-mezőből kell megjeleníteni, nem újraírni.

## 5. Szabályok, amiket kérlek tartsatok be

- **Ne írd át a `:root` tokeneket** a plugin `public.css`-ében. Az új CSS csak hozzáad.
- **Ne nyúlj a szülő témához** (`ollie`).
- A meglévő PHP logikát (lekérdezések, kosár, jogosultság, nonce) **tartsd meg**.
- Minden felhasználói szöveg fordítható és escape-elt. Text domain: plugin → `bma-ajanlatkeres`, gyerektéma → `bma-engineering`. Ne vezess be újat.
- Az ikonok inline SVG-k (stroke alapú, 20/24 px rács). Ne cseréld emojira.
- A sarokkerekítés 0 px – szándékos, ne adj hozzá `border-radius`-t.
- Új CSS-t a media query-k **elé** írj a `bma-v2.css`-ben.

### Akadálymentesség – ezt kérlek a kódban rendbe tenni

- A tétel-választó valódi `<input type="checkbox">` legyen, `<label>`-lel összekötve
  (`id` / `for`), a `.task` csak a vizuális burkolat. Ugyanez a hozzájárulás-jelölőkre.
- Minden űrlapmezőnek legyen társított `<label for="…">`.
- A díszítő SVG-k kapjanak `aria-hidden="true"`-t.
- A lépéssáv `aria-current="step"`-pel, a fülek `role="tab"` / `aria-selected` párral.
- A hamburger gomb `aria-expanded` és `aria-controls` attribútumokkal vezérelje a menüt.
- **Kontraszt:** a `#ee7643` fehér háttéren 2,9:1 – a WCAG AA (4,5:1) alatt. A nagy
  gombfeliratokon elmegy, de a kis narancs szövegeknél (linkek, `.eyebrow`, „Letöltés")
  érdemes sötétebbet használni (kb. `#b5461a`). Márka-döntés, ezért nem írtam át a
  tervben – ha rábólintotok, egy tokennel megoldható.

## 6. Amit nektek kell kitölteni

`[SZÖGLETES ZÁRÓJELBEN]` szerepel minden olyan adat, amit nem tudtam a jelenlegi oldalról
kiolvasni:

- `[X] munkanapon belül` – tényleges válaszidő az ajánlatra
- `[X–Y] hét` – tipikus átfutási idő szolgáltatásonként
- `[X] MB` – feltölthető fájlméret korlát
- `[MAGYAR / ROMÁN]` – kiszolgálási nyelvek
- `[X] napig érvényes`, `[ÖSSZEG] EUR` – ajánlat érvényessége és összege a portálon
- `[HELYRAJZI SZÁM]`, `[X] m²`, `[X] m³/nap` – a portál mintaadatai
- `[ÜGYFÉL NEVE]`, `[email cím]` – a portál üdvözlő sora
- `[LOGÓ]`, `[Projekt fotó]`, `[Fotó 1–3]` – logó és képek helye
- `[X] hónap` – a referencia projekt átfutási ideje
  (a `[TELEPÜLÉS]` és `[ÉVSZÁM]` **már nem helyőrző**: `_bma_location` és `_bma_year` metából jön)
- A referencia projekt „A feladat / Amit csináltunk / Eredmény" szövegei
- GYIK válaszszövegek a szolgáltatás aloldalon

## 7. Amit ez a csomag NEM tartalmaz

- A tervet a böngészőben látott oldalból építettem; a PHP forrást nem én olvastam.
  A `FELDERITES-UTAN.md` tartalmazza a Cursor felderítéséből származó tényleges pathokat
  és handle-eket – **ütközés esetén az a mérvadó**.
- A **referenciák lista és a projekt oldal** tervét a főoldali referencia-kártyák adataiból
  építettem, mert a `/referenciak/` oldalt nem tudtam megnyitni (a böngésző-hozzáférés
  el lett utasítva). Ha a jelenlegi oldal ettől eltérő mezőket vagy szűréseket használ,
  szóljatok, és igazítom.
- Nincs benne adatbázis-migráció, e-mail sablon, sem fizetési folyamat.
- A mobil menü (hamburger) nyitó-záró JS-e nincs megírva – a gomb és a hely kész.
