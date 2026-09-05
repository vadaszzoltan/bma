# BMA i18n / Polylang export

## Olvasható exportok
- `content-hu.md` – magyar forrástartalom (oldalak, CPT, státuszok, e-mailek, SEO)
- `translations.md` – RO / EN fordítások összefoglalója
- `db-extras.json` – szolgáltatáselemek + dinamikus mezők
- `ui-strings-complete.json` – teljes publikus UI fordításkatalógus (HU → RO/EN, ~450 string)
- `ui-strings-source.json` – a kódból kinyert gettext forrásszövegek listája

## Gépi formátumok (Polylang import)
- `content-hu.json` – forrás snapshot
- `translations.json` – teljes RO/EN katalógus (oldalak + overlay + ui_strings)

## Scriptek
```bash
# 1) Export (WP-CLI a Docker konténerben)
docker cp scripts/i18n-export-content.php bma-wordpress-1:/tmp/
docker exec bma-wordpress-1 php /tmp/wp-cli.phar --allow-root --path=/var/www/html eval-file /tmp/i18n-export-content.php

# 2) Fordításkatalógus építése (beolvassa a ui-strings-complete.json-t is)
python3 scripts/i18n-build-translations.py

# 3) Polylang feltöltés
docker cp docs/i18n/translations.json bma-wordpress-1:/var/www/html/wp-content/bma-i18n-export/translations.json
docker cp scripts/i18n-setup-polylang.php bma-wordpress-1:/tmp/
docker exec bma-wordpress-1 php /tmp/wp-cli.phar --allow-root --path=/var/www/html eval-file /tmp/i18n-setup-polylang.php
```

## Nyelvek
- HU (alap): `/`
- RO: `/ro/...`
- EN: `/en/...`

## Megjegyzés
A shortcode-os oldalak (főoldal, szolgáltatások, kapcsolat, jogi oldalak stb.) szövegei gettext + `bma_i18n_ui_strings` overlay-en keresztül fordulnak. Új UI-szöveg után frissítsd a `ui-strings-complete.json`-t, majd futtasd a 2–3. lépést.
