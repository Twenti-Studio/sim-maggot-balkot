#!/usr/bin/env sh
set -eu

ROOT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
cd "$ROOT_DIR"

COMPOSE_FILES="-f compose.yaml -f compose.prod.yaml"
RUN_SSL=0

if [ "${1:-}" = "--ssl" ]; then
    RUN_SSL=1
fi

if [ ! -f .env ]; then
    cp .env.production.example .env
    echo "Created .env from .env.production.example. Review passwords before running again."
    exit 1
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    if command -v openssl >/dev/null 2>&1; then
        APP_KEY="base64:$(openssl rand -base64 32)"
    else
        APP_KEY="$(docker run --rm php:8.4-cli-alpine php -r 'echo "base64:".base64_encode(random_bytes(32));')"
    fi

    if grep -q '^APP_KEY=' .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
    else
        printf '\nAPP_KEY=%s\n' "$APP_KEY" >> .env
    fi
    echo "Generated APP_KEY in .env."
fi

if grep -q '^DB_PASSWORD=change-this-database-password$' .env || grep -q '^INITIAL_ADMIN_PASSWORD=change-with-a-strong-password$' .env; then
    echo "Update DB_PASSWORD and INITIAL_ADMIN_PASSWORD in .env before deployment."
    exit 1
fi

docker compose $COMPOSE_FILES build
docker compose $COMPOSE_FILES up -d db app queue web
docker compose $COMPOSE_FILES exec app php artisan migrate --force --seed
docker compose $COMPOSE_FILES ps

if [ "$RUN_SSL" -eq 1 ]; then
    docker compose $COMPOSE_FILES --profile certbot run --rm --entrypoint sh certbot -lc '
        if [ -d /etc/letsencrypt/live/simaggotbalkot.com ] && [ ! -d /etc/letsencrypt/archive/simaggotbalkot.com ]; then
            rm -rf /etc/letsencrypt/live/simaggotbalkot.com /etc/letsencrypt/renewal/simaggotbalkot.com.conf
        fi
    '
    docker compose $COMPOSE_FILES --profile certbot run --rm certbot
    docker compose $COMPOSE_FILES restart web
fi
