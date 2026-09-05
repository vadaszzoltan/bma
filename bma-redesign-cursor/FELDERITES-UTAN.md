# Felderítés utáni kiegészítés – ELŐBB EZT OLVASD

**Ütközés esetén ez a fájl a mérvadó.**

A projekt tényleges felépítésének megismerése után készült, és felülírja a `README.md`
néhány feltételezését.

**Dokumentum verzió: v3 · 2026-09-05.** Ha a fájlon belül két hely ellentmondana egymásnak,
a **kisebb sorszámú, tematikus szakasz** a mérvadó (2., 6., 7. pont), nem a 12. pont
kérdéslistája.

---

## 1. A kód nem a témában van, hanem a pluginban

A `README.md` úgy fogalmaz, hogy a szekció-markup a gyerektéma template-jeibe kerül. **Ez
téves.** A megjelenítést a `bma-ajanlatkeres` plugin shortcode-jai adják:

| Terv-fájl | Cél PHP fájl |
|---|---|
| `markup/01-fooldal.html` → hero | `plugins/bma-ajanlatkeres/templates/frontend/home-hero.php` |
| → szolgáltatások | `…/frontend/home-services.php` |
| → „Egy projekt ritkán…" | `…/frontend/home-multi.php` |
| → előnyök | `…/frontend/home-benefits.php` |
| → folyamat | `…/frontend/home-process.php` |
| → referenciák | `…/frontend/home-references.php` |
| → rólunk | `…/frontend/home-about.php` |
| → záró sáv | `…/frontend/home-closing.php` |
| `markup/02-szolgaltatasok.html` | `…/frontend/services-list.php` + `partials/select-card.php`, `partials/selection-bar.php` |
| `markup/03-ajanlatkeres.html` | `…/frontend/quote-cart.php` és `…/frontend/quote-form.php` (lásd 4. pont) |
| `markup/04-ugyfelportal.html` | `…/frontend/client-portal.php` |
| `markup/05-referenciak.html` | `…/frontend/references-list.php` |
| `markup/06-referencia-projekt.html` | `templates/single-bma_reference.php` |
| `markup/07-szolgaltatas-aloldal.html` | `templates/single-bma_service.php` |

**A CSS marad a gyerektémában** (`bma-engineering-child/assets/css/bma-v2.css`) – az a rész
a README szerint helyes.

## 2. A CSS KÉT fájl – ne olvaszd össze őket

A gyerektémában már volt egy korábbi, 611 soros `bma-v2.css`, amiben **WordPress-illesztő
szabályok** is voltak (WP menü markup, logó blokk, topbar, Polylang váltó, hero fotóréteg,
referencia thumbnail, egy `@media (max-width: 960px)` blokk). Ezek nem a terv részei, de
nélkülük eltörik a megjelenés, amíg a template-ek át nem állnak.

Ezért a CSS **két fájl**, és így is marad:

| Fájl | Tartalom | Ki írja |
|---|---|---|
| `assets/css/bma-v2.css` | a csomagbeli design CSS, változtatás nélkül | **minden új csomag felülírja** – kézzel SOHA ne szerkeszd |
| `assets/css/bma-v2-integration.css` | téma-specifikus illesztések a meglévő WP markuphoz | a fejlesztő (Cursor) – a csomag nem nyúl hozzá |

Enqueue sorrend: `bma-theme-layout` → `bma-v2` → `bma-v2-integration`
(az utóbbi függősége `bma-v2`, verzió `filemtime()`).

**Ideiglenes elemek az integration fájlban** – jelöld TODO kommenttel, és töröld, amint a
megfelelő lépés elkészült:

| Szabály | Mikor törlendő | Miért |
|---|---|---|
| `.bma-lang-switcher` (+ `current-lang`) | a 3. lépés (nyelvválasztó) után | akkor már a `.langswitch` markup lesz élesben |
| `.topbar-inner { justify-content: space-between }` | a 3. lépés után | a terv `margin-left: auto`-val pozicionálja a váltót |
| a teljes `@media (max-width: 960px)` blokk | az utolsó template átvezetése után | ütközik a csomag 1100 / 900 / 640 töréspontjaival |

A többi (WP menü, logó, topbar kapcsolatok, `.hero-media img`, `.ref-img img`,
`.bma-site-main`) **marad** – ezek a téma markupjához tartoznak, nem a tervhez.

## 2.b Az enqueue handle más, és a CSS lehet, hogy már be van kötve

- A `theme.css` handle-je **`bma-theme-layout`**, nem `bma-child-theme` (a README-ben lévő
  név nem létezik). A `style.css` handle-je `bma-child-style`.
- A felderítés szerint **`bma-v2` már be van kötve**, `bma-theme-layout` függőséggel.
  **Mielőtt bármit írnál:** ellenőrizd, hogy a `bma-engineering-child/assets/css/bma-v2.css`
  fájl létezik-e, és tartalmilag megegyezik-e a csomagban lévővel. Ha eltér, a csomagbeli
  verzióval írd felül. Ha nem létezik, csak akkor másold be.
- A viewport meta megvan a `header.php`-ban – nem kell hozzányúlni.

## 3. A főoldal MÁR RÉSZBEN át van írva – mindegyik szekció

A shortcode-sorrend a live főoldalon (page ID 10) megerősítve:
`hero → services → multi → process → benefits → references → about → closing`
– **pontosan a `markup/01-fooldal.html` sorrendje**, tehát átrendezni nem kell.
(A seeder korábban `benefits → process`-t mutatott; az élő tartalom a mérvadó.)

**A régi külső burkolók (`.bma-section`, `.bma-home-*`) maradjanak.** A `bma-v2.css` a
plugin `public.css` után töltődik, tehát a terv `.bma-section` szabályai felülírják a
régit. Ha eltávolítod a burkolókat, más törhet el.

Nem csak a hero és a closing: **az összes `home-*.php` tartalmaz már v2 osztályokat**
(`home-hero.php` → `.hero`, `home-closing.php` → `.closing`, a többiben belső v2 osztályok).
**Ne írd felül vakon.**

Csináld így: minden főoldali szekciónál előbb hasonlítsd össze a meglévő PHP markupot a
`markup/01-fooldal.html` megfelelő blokkjával, és **sorold fel az eltéréseket**, mielőtt
módosítanál. Ahol a meglévő már megegyezik a tervvel, ott ne nyúlj hozzá.

## 4. Három oldal van a folyamatban, nem kettő

A felderítés szerint: `/szolgaltatasok/` → `/ajanlatkosar/` (`[bma_quote_cart]`) →
`/ajanlatkeres/` (`[bma_quote_form]`). A terv négy lépése **így képződik le a meglévő
oldalakra – új oldalt nem kell létrehozni**:

| Terv lépése | Meglévő oldal | Template |
|---|---|---|
| 01 Szolgáltatások | `/szolgaltatasok/` | `services-list.php` |
| 02 Kosár áttekintése | `/ajanlatkosar/` | `quote-cart.php` |
| 03 Projekt adatai + kapcsolattartás + beküldés | `/ajanlatkeres/` | `quote-form.php` |
| 04 Visszaigazolás | beküldés utáni nézet | `quote-form.php` (submitted állapot) |

A `markup/03-ajanlatkeres.html` négy `<sc-if>` blokkja ennek megfelelően osztandó szét:
a „Szolgáltatások” lépés a `services-list.php`-hez tartozik (ott már van kiválasztó),
a „Projekt adatai” és az „Ellenőrzés és beküldés” a `quote-form.php`-be, a „Visszaigazolás”
a beküldés utáni állapotba. A kosár-összegző panel (`.summary`) mindhárom oldalon
ugyanaz a részlet legyen – **egy partial**, ne három másolat.

A lépéssáv (`.steps` / `.stepchip`) mindhárom oldal tetején jelenjen meg, a megfelelő
lépés `is-now`, az előzőek `is-done` állapottal, és a `is-done` lépések linkeljenek vissza.

## 5. A kosár működése – amit meg kell tartani

- `includes/class-cart.php` (`BMA_AJ_Cart`), cookie `bma_cart_id` + transient `bma_cart_{id}`
- AJAX: `bma_add_to_cart`, `bma_remove_from_cart`, nonce `bma_aj_public`
- Beküldés: `bma_submit_quote_request`
- JS: `assets/js/selection.js` (hozzáadás), `assets/js/public.js` (eltávolítás, beküldés)

**Ezt a logikát ne írd újra.** A terv annyit kér, hogy a `bma_add_to_cart` /
`bma_remove_from_cart` válasza után a JS frissítse a `.summary` panelt, a fejléc
`.cart-count` értékét és a mobil `.mobilebar` feliratát is – ma a fejléc számlálója nem
frissül. Ha a válasz nem tartalmazza a kosár aktuális tartalmát, egészítsd ki vele.

## 6. Referenciák: nincs „szolgáltatás” taxonómia – a szűrő máshogy épül

A `markup/05-referenciak.html` két szűrőcsoportot mutat (Típus, Szolgáltatás). A meglévő
adatszerkezet:

A DB-lekérdezés megerősítette: a `bma_reference_category` taxonómia pontosan a típust
tartalmazza – **Lakossági** (`lakossagi`, 2 db) és **Üzleti** (`uzleti`, 2 db). Ugyanez az
adat a `_bma_project_type` metában is szerepel.

| Szűrő | Adatforrás | Megjegyzés |
|---|---|---|
| **Típus (lakossági / üzleti)** | **`bma_reference_category` taxonómia** | valódi `tax_query`, indexelt, admin felület már van hozzá |
| Szolgáltatás | `_bma_related_services` post meta (service post ID-k) | ehhez nincs taxonómia, és **ne is hozz létre újat** |
| Helyszín | `_bma_location` post meta | létezik |
| Év | `_bma_year` post meta | |

**Ez felülírja a korábbi verziót**, ami a típust is metából kérte: mivel a taxonómia
létezik és fel van töltve, azt használd.

A szolgáltatás-szűrőhöz **ne hozz létre taxonómiát**. A referenciák száma kicsi: kérdezd le
a típus szerint szűrt bejegyzéseket, és a `_bma_related_services` metára PHP-ben szűrj –
megbízhatóbb, mint szerializált metára `LIKE`-olni. A szűrő állapota kerüljön a query
stringbe (pl. `?tipus=lakossagi&szolgaltatas=12`), hogy megosztható és visszalapozható legyen.

**Nyitott kérdés a 9. lépéshez:** ír-e még bárhol a kód a `_bma_project_type` metába? Ha
igen, az duplikáció a taxonómiával – nézd meg, és javasolj egyet, amelyik marad. Most ne
nyúlj hozzá.

## 7. Referencia projekt: URL és mezők

- Az útvonal **`/referencia/{slug}/`**, nem `/referenciak/{slug}/`. A morzsamenüben és a
  linkekben ezt használd.
- A „Projekt adatai" doboz mezői meglévő adatokból jönnek, tehát **egyik sem helyőrző**:
  - Típus → `bma_reference_category` taxonómia (lásd 6. pont)
  - Helyszín → `_bma_location` meta – **létezik** (`class-reference-cpt.php`, admin „Helyszín”
    mező). A markupban lévő `[TELEPÜLÉS]` NEM helyőrző.
  - Év → `_bma_year` meta – az `[ÉVSZÁM]` sem helyőrző
  - Szolgáltatások → `_bma_related_services` meta
- Egyedül az „Átfutás **[X] hónap**" marad helyőrző, arra nincs mező.
- A jobb oldali „Ehhez kapcsolódó szolgáltatások" panel pontosan a `_bma_related_services`
  mezőre épül – a „Hozzáadás" gombok a meglévő `bma_add_to_cart` action-t hívják.

## 8. Fizetés VAN a rendszerben – javítás a portálon

A felderítés `bma_create_stripe_session` és `bma_accept_quote` action-öket talált, tehát a
fizetés része a folyamatnak. A `README.md` és a `VALTOZASOK.md` tévesen állítja, hogy nincs.

**Javítva:** a portál negyedik csempéje és füle **„Fizetésre vár"** (nem „Lezárt”), és a
lista tartalmaz egy ilyen állapotú kérést is, „Teendő: díjbekérő rendezése" következő
lépéssel. A frissített terv a vásznon már ezt mutatja.

## 9. Nyelvválasztó: már létezik, csak lecserélendő

**A jelenlegi hívás:** `pll_the_languages()` (nem `raw`), burkoló `<div class="bma-lang-switcher">`,
opciók: `dropdown => 0`, `show_flags => 0`, `show_names => 1`, `display_names_as => 'slug'`,
`force_home => 0`. A `force_home => 0` helyes – ne írd át, ez viszi az aktuális oldal
fordítására.

**Hiányzó fordítás:** a `raw` tömb `no_translation` kulcsa jelzi. Ilyenkor a nyelv ne link
legyen, hanem `<span class="lang lang--off">` – a `.lang--off` stílus (halvány, nem
kattintható) benne van a `bma-v2.css`-ben.

**Felirat:** a `slug` nagybetűsítve (`HU` / `RO` / `EN`), hogy nyelvfüggetlen maradjon.

`includes/class-i18n.php` + a headerben `pll_the_languages` – **Polylang**, WPML nincs.
A feladat tehát nem új integráció, hanem a meglévő kimenet lecserélése a `.langswitch`
markupra:

```php
$langs = pll_the_languages( array( 'raw' => 1 ) );
```

és abból építsd fel a `<div class="langswitch">…</div>` szerkezetet (aktív nyelv: `is-on`
+ `aria-current="true"`, minden linkre `hreflang`). A `<head>`-beli alternate linkeket a
Polylang alapból kiteszi – ellenőrizd, hogy megvannak-e mind a háromra plusz `x-default`.

## 10. Text domain – ismert, ne vezess be újat

| Hol | Text domain |
|---|---|
| Plugin (`bma-ajanlatkeres`) | `bma-ajanlatkeres` |
| Gyerektéma | `bma-engineering` |

## 11. Helyszín-meta LÉTEZIK

A `bma_reference` CPT-n van `_bma_location` meta. A `06-referencia-projekt.html`-ben lévő
`[TELEPÜLÉS]` helyőrző tehát **nem helyőrző**: ebből a mezőből töltendő. Ugyanígy az
`[ÉVSZÁM]` a `_bma_year`-ből.

## 13. Új mezők, amiket a bekötés hozott létre (2026-09-05, 6. lépés)

A szolgáltatás aloldal két szekciójának nem volt adatforrása, ezért a `bma_service` CPT
új metákat kapott. **Ezeket az ügyfélnek kell kitöltenie mind a négy szolgáltatásnál**,
különben a szekciók nem jelennek meg.

| Meta | Hol jelenik meg | Formátum |
|---|---|---|
| `_bma_needs_required` | „Mire lesz szükség?" → Kötelező | soronként egy tétel |
| `_bma_needs_optional` | → Ha rendelkezésre áll | soronként egy tétel |
| `_bma_needs_later` | → Később is pótolható | soronként egy tétel |
| `_bma_faq` | GYIK | `K: …` / `V: …` blokkok, üres sorral elválasztva |

**Megjelenítési szabály:** a „Mire lesz szükség?" szekció akkor jelenjen meg, ha a három
mező közül **legalább egy** ki van töltve – és csak a kitöltött oszlopok renderelődjenek.
(Ne kösd mindhárom mező meglétéhez: egy üres „Később is pótolható" oszlop miatt ne tűnjön
el az egész szekció.) A GYIK csak érvényes K/V párokkal jelenjen meg.

A GYIK `<details>` elemekkel készült – ez jobb, mint a tervben lévő statikus lista
(billentyűzettel kezelhető, JS nélkül működik). Marad így.

## 12. Amit még tisztázni kell (kérdezz, ne találgass)

1. Melyik `home-*.php` van már teljesen átvezetve, és melyik csak részben? (fájlonként
   kérem az összehasonlítást a `markup/01-fooldal.html` blokkjaival)
2. **ELDÖNTVE (lásd 6. pont): a típusszűrő a `bma_reference_category` taxonómiára épül.**
   Ami nyitva maradt: ír-e még bárhol a kód a `_bma_project_type` metába? Ha igen, az
   duplikáció – a 9. lépésnél nézd meg, és javasolj egyet, amelyik marad. Addig ne nyúlj hozzá.
3. A `/ajanlatkosar/` és a `/ajanlatkeres/` közötti jelenlegi átmenet: hol történik a
   beküldés, és hol jelenik meg a visszaigazolás?
