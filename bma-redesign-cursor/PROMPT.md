# Bemásolható Cursor-prompt

> A csomag a Letöltések mappában van: `bma-redesign-cursor.zip`.
> Cursor: **Agent mód**, ne Ask.

---

## Első üzenet (kicsomagolás + ellenőrzés + feltérképezés)

```
A terv-csomag a Letöltések mappámban van: bma-redesign-cursor.zip
(macOS/Linux: ~/Downloads/bma-redesign-cursor.zip,
Windows: %USERPROFILE%\Downloads\bma-redesign-cursor.zip)

1) CSOMAG
   Csomagold ki a projekt gyökerébe úgy, hogy a meglévő bma-redesign-cursor/ mappát
   TELJESEN felülírja. Ellenőrizd, hogy megvan:
     FELDERITES-UTAN.md, README.md, SZOVEGVALTOZASOK.md, VALTOZASOK.md, PROMPT.md,
     css/bma-v2.css,
     markup/01-fooldal.html, 02-szolgaltatasok.html, 03-ajanlatkeres.html,
     04-ugyfelportal.html, 05-referenciak.html, 06-referencia-projekt.html,
     07-szolgaltatas-aloldal.html
   Ha BÁRMELYIK hiányzik: állj meg, írd ki mi hiányzik, és ne csinálj tovább semmit.

2) OLVASÁS – ebben a sorrendben
   FELDERITES-UTAN.md   (ez felülírja a README feltételezéseit – ütközésnél EZ a mérvadó)
   README.md
   SZOVEGVALTOZASOK.md
   VALTOZASOK.md
   PROMPT.md

3) 1. LÉPÉS – CSAK FELTÉRKÉPEZÉS
   NE módosíts egyetlen fájlt sem. NE futtass adatbázis-parancsot.
   Írd ki:
   - létezik-e wp-content/themes/bma-engineering-child/assets/css/bma-v2.css,
     és miben tér el a csomagbeli css/bma-v2.css-től (a különbség lényege, nem teljes diff)
   - a home-*.php fájlok jelenlegi markupja mennyire egyezik a markup/01-fooldal.html
     megfelelő blokkjaival – fájlonként: MEGEGYEZIK / RÉSZBEN / NINCS ÁTVEZETVE
   - a services-list.php, quote-cart.php, quote-form.php, client-portal.php,
     references-list.php, single-bma_reference.php, single-bma_service.php
     jelenlegi állapota ugyanígy
   - van-e _bma_location meta a bma_reference CPT-n, és mik a bma_reference_category
     taxonómia jelenlegi értékei
   - a header.php-ban a Polylang nyelvváltó jelenlegi kimenete (melyik függvény, milyen markup)
   - mit NEM találtál meg

   Ha valamiben bizonytalan vagy: kérdezz, NE találgass.
```

## Következő üzenetek (egyesével, külön committal)

Minden lépés után: `git diff` → átnézed → commit. Csak akkor engedd a következőt.

```
Rendben. Most CSAK a 2. lépést csináld meg, és állj meg.
```

| Lépés | Mit csinál | Fájlok |
|---|---|---|
| **2** | CSS: a meglévő `bma-v2.css` felülírása a csomagbelivel (ha eltér); enqueue ellenőrzése (`bma-v2`, függőség `bma-theme-layout`) | gyerektéma |
| **3** | Nyelvválasztó: a meglévő Polylang-kimenet lecserélése a `.langswitch` markupra a fejlécben és a láblécben | `header.php`, `footer.php` / `class-i18n.php` |
| **4** | Főoldal szekciónként – **előbb eltérés-lista, csak utána írás** | `home-*.php` |
| **5** | Szolgáltatás lista + a tételválasztó **közös partialba** emelése | `services-list.php`, `partials/*` |
| **6** | Szolgáltatás aloldal (mind a 4 területre) | `single-bma_service.php` |
| **7** | Kosár + ajánlatkérő űrlap + visszaigazolás, lépéssávval | `quote-cart.php`, `quote-form.php` |
| **8** | Ügyfélportál (lista + részletes nézet) | `client-portal.php` |
| **9** | Referenciák lista (meta-alapú szűrő) + projekt oldal | `references-list.php`, `single-bma_reference.php` |
| **10** | Mobil menü JS (`.burger`): nyitás/zárás, `aria-expanded`, Esc, fókuszcsapda, benne a nyelvválasztó | téma JS |

## Szabályok – minden lépésre érvényes

- **Ne találgass.** Ha egy adat nem derül ki a kódból, kérdezz. A `[SZÖGLETES ZÁRÓJELES]`
  helyőrzőket hagyd benne, és a végén listázd, mit kell kitölteni.
- **Ne írd át azt a szöveget**, ami nincs a `SZOVEGVALTOZASOK.md`-ben felsorolva. Az a
  meglévő stringből vagy adatbázis-mezőből jöjjön.
- **Ne írj felül vakon.** Ahol a meglévő PHP már megegyezik a tervvel, ne nyúlj hozzá.
- **Ne nyúlj a szülő témához** (`ollie`), és **ne írd felül a `:root` tokeneket** a plugin
  `public.css`-ében.
- **Tartsd meg a kosár logikát**: `BMA_AJ_Cart`, `bma_add_to_cart`, `bma_remove_from_cart`,
  `bma_submit_quote_request`, nonce `bma_aj_public`. A terv csak a megjelenítést változtatja.
- **Ne hozz létre új taxonómiát.** A referencia-szűrő meglévő metákra épül.
- **Text domain**: plugin → `bma-ajanlatkeres`, téma → `bma-engineering`. Ne vezess be újat.
- **Escape**: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` ahol HTML kell.
- **Ne futtass adatbázis-parancsot** (`wp db`, `DROP`, `TRUNCATE`, migráció) engedély nélkül.
- Minden lépés végén írd le egy-két mondatban, mit változtattál és melyik fájlban.
