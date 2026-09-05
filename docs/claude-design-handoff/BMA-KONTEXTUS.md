# BMA-specifikus kontextus (add oda a Designnak ehhez a projekthez)

Ezek **a repo jelenlegi tényei**. Claude Design ezekre építsen; ne találjon ki mást.

## Stack

| Elem | Hol |
|------|-----|
| Gyerektéma | `wp-content/themes/bma-engineering-child/` (szülő: `ollie`) |
| Téma layout CSS | `…/assets/css/theme.css` — enqueue handle: **`bma-theme-layout`** |
| `style.css` handle | **`bma-child-style`** |
| Redesign CSS cél | `…/assets/css/bma-v2.css` — handle: **`bma-v2`** (lehet, hogy már be van kötve) |
| Plugin | `wp-content/plugins/bma-ajanlatkeres/` |
| Plugin public CSS / tokenek | `…/assets/css/public.css` (`:root` — **ne írd felül**) |
| i18n | **Polylang** (WPML nincs) |
| Text domain plugin | `bma-ajanlatkeres` |
| Text domain téma | `bma-engineering` |

## Hol van a markup a kódban?

**Plugin shortcode-ok + CPT template-ek**, nem a téma `page-*.php` fájljai.

Főoldal szekciók → `templates/frontend/home-*.php`  
Lista / űrlap / portál → `templates/frontend/*.php`  
Szolgáltatás single → `templates/single-bma_service.php` (URL: `/szolgaltatas/{slug}/`)  
Referencia single → `templates/single-bma_reference.php` (URL: `/referencia/{slug}/`)

## Ajánlatkérő folyamat — 3 oldal, nem 1

| Lépés | URL | Shortcode / template |
|-------|-----|----------------------|
| Kiválasztás | `/szolgaltatasok/` | `[bma_services_list]` → `services-list.php` |
| Kosár | `/ajanlatkosar/` | `[bma_quote_cart]` → `quote-cart.php` |
| Űrlap + visszaigazolás | `/ajanlatkeres/` | `[bma_quote_form]` → `quote-form.php` |
| Portál | `/ugyfelportal/` | `[bma_client_portal]` → `client-portal.php` |
| Referenciák lista | `/referenciak/` | `[bma_references_list]` → `references-list.php` |

## Kosár — megtartandó

- Osztály: `BMA_AJ_Cart` (`includes/class-cart.php`)
- Cookie + transient
- AJAX: `bma_add_to_cart`, `bma_remove_from_cart`, nonce `bma_aj_public`
- Beküldés: `bma_submit_quote_request`
- JS: `selection.js`, `public.js`

A terv UI-ja frissítse a `.summary` / `.cart-count` / `.mobilebar` megjelenést — **ne írj új kosár backendet**.

## Referenciák adatok

| UI szűrő / mező | Forrás |
|-----------------|--------|
| Típus | meta `_bma_project_type` (nem új tax) |
| Kapcsolódó szolgáltatások | meta `_bma_related_services` |
| Helyszín | meta **`_bma_location`** (létezik) |
| Év | meta `_bma_year` |
| Kategória tax | `bma_reference_category` — jelenleg tipikusan **Lakossági / Üzleti** |

**Ne javasolj új taxonómiát** a szolgáltatás-szűrőhöz.

## Főoldal státusz (részleges bekötés)

Már v2 irányba húzva: `home-hero.php` (`.hero`), `home-closing.php` (`.closing`), és a többi `home-*.php` belső v2 osztályokkal.  
Cursor / Design: **ne írj felül vakon** — előbb PHP ↔ markup diff.

## Fizetés

Van a rendszerben (`bma_accept_quote`, `bma_create_stripe_session`). A portál UI-ban a „Fizetésre vár” állapot releváns — ne írd, hogy „nincs fizetés”.

## Design tokenek (változatlan)

```
--bma-primary  #0e2356
--bma-accent   #ee7643
--bma-bg       #f7f8fa
--bma-text     #0e2356
--bma-muted    #6b7280
--bma-border   #e6e9ef
--bma-radius   0px
--bma-font-heading  Manrope
--bma-font-body     Source Sans 3
```

Új tipikus token a v2 CSS-ben: `--bma-accent-dark: #d45e2c`.