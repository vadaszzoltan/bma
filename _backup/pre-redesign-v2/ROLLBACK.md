# Theme redesign experiment – rollback

Branch: `experiment/theme-redesign-v2`

## Visszalépés (teljes)

```bash
# 1) Ág elhagyása (vissza a korábbi ágra)
git checkout cursor/bma-wordpress-ajanlatkeres-rendszer

# 2) Téma visszamásolása a backupból (ha az ágváltás nem elég)
rsync -a --delete \
  _backup/pre-redesign-v2/bma-engineering-child/ \
  wp-content/themes/bma-engineering-child/

rsync -a \
  _backup/pre-redesign-v2/plugin-frontend/frontend/ \
  wp-content/plugins/bma-ajanlatkeres/templates/frontend/

cp _backup/pre-redesign-v2/plugin-frontend/single-bma_service.php \
   wp-content/plugins/bma-ajanlatkeres/templates/
cp _backup/pre-redesign-v2/plugin-frontend/single-bma_reference.php \
   wp-content/plugins/bma-ajanlatkeres/templates/
cp _backup/pre-redesign-v2/plugin-public.css \
   wp-content/plugins/bma-ajanlatkeres/assets/css/public.css
```

## Csak a v2 CSS kikapcsolása

Töröld / kommenteld a `functions.php` `bma-v2` enqueue blokkját, vagy töröld:
`wp-content/themes/bma-engineering-child/assets/css/bma-v2.css`
