#!/bin/sh
# Container entrypoint: prepares Laravel at *runtime*, then starts the server.
# (.env is not in the image on purpose - config comes from environment variables.)
set -e

cd /var/www/html

# 1) APP_KEY - generate a throw-away key if the caller did not provide one.
#    For a stable key (sessions/cookies survive restarts) run with:
#      docker run -e APP_KEY=base64:... ...
if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show --no-ansi | tail -n 1)"
    export APP_KEY
    echo "[entrypoint] APP_KEY not set -> generated a temporary key for this container."
fi

# 2) SQLite (default for the container demo): create the DB file on first start.
NEW_DB=0
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    export DB_DATABASE
    if [ ! -f "$DB_DATABASE" ]; then
        mkdir -p "$(dirname "$DB_DATABASE")"
        touch "$DB_DATABASE"
        NEW_DB=1
        echo "[entrypoint] Created SQLite database at $DB_DATABASE"
    fi
fi

# 3) Create tables.
php artisan migrate --force

# 4) Seed sample data only on a brand-new SQLite DB (disable with -e DB_SEED=false).
if [ "$NEW_DB" = "1" ] && [ "${DB_SEED:-true}" = "true" ]; then
    echo "[entrypoint] Seeding sample data..."
    php artisan db:seed --force
fi

# 5) Hand over to the main process (CMD) so it receives signals directly.
exec "$@"
