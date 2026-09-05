#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi

echo "Starting Docker containers..."
docker compose up -d

echo "Fixing uploads directory permissions..."
docker compose exec -T -u root wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null || true
docker compose exec -T -u root wordpress mkdir -p /var/www/html/wp-content/uploads/bma-documents/temp 2>/dev/null || true
docker compose exec -T -u root wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads/bma-documents 2>/dev/null || true

echo ""
echo "WordPress: http://localhost:8081"
echo "MailHog:   http://localhost:8026"
echo ""
echo "Next steps:"
echo "  1. Complete WordPress installation in the browser"
echo "  2. Install and activate Twenty Twenty-Four parent theme (or your purchased template)"
echo "  3. Activate 'BMA Engineering Child' theme"
echo "  4. Activate 'BMA Ajánlatkérés' plugin"
echo "  5. Go to Settings > Permalinks and click Save"
echo "  6. Configure BMA settings under BMA Ajánlatkérés > Beállítások"
