#!/usr/bin/env bash
# Demo tartalom betöltése a futó WordPress konténerben.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if ! docker compose ps --status running wordpress 2>/dev/null | grep -q wordpress; then
  echo "A WordPress konténer nem fut. Indítsd el: docker compose up -d"
  exit 1
fi

docker compose exec -T wordpress php -r "
require '/var/www/html/wp-load.php';
require_once WP_CONTENT_DIR . '/plugins/bma-ajanlatkeres/includes/class-seeder.php';
BMA_AJ_Seeder::reseed();
echo \"Demo tartalom betöltve.\n\";
"

echo ""
echo "Frissítsd a böngészőt: http://localhost:8080"
