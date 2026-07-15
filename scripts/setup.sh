#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT"

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi

echo "Starting Docker containers..."
docker compose up -d

echo ""
echo "WordPress: http://localhost:8080"
echo "MailHog:   http://localhost:8025"
echo ""
echo "Next steps:"
echo "  1. Complete WordPress installation in the browser"
echo "  2. Install and activate Twenty Twenty-Four parent theme (or your purchased template)"
echo "  3. Activate 'BMA Engineering Child' theme"
echo "  4. Activate 'BMA Ajánlatkérés' plugin"
echo "  5. Go to Settings > Permalinks and click Save"
echo "  6. Configure BMA settings under BMA Ajánlatkérés > Beállítások"
