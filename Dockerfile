# =====================================================================
#  PLcare - Laravel 9 API
#
#  Layers are ordered from "changes rarely" -> "changes often", so a
#  small code edit only rebuilds the last few (cheap) layers:
#
#    1. OS packages + PHP extensions        (almost never changes)
#    2. Composer binary                     (almost never changes)
#    3. composer.json + composer.lock  ->  composer install   (slow!)
#         only re-runs when a dependency changes
#    4. Application source code             (changes on every edit)
#    5. Autoloader, permissions, entrypoint (fast)
# =====================================================================
FROM php:8.2-cli

# ---- 1. System packages & PHP extensions ----------------------------
# git/unzip are used by Composer. pdo_mysql lets you point the app at
# MySQL (-e DB_CONNECTION=mysql ...). pdo_sqlite is already in the image.
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && docker-php-ext-install pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# ---- 2. Composer ----------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

WORKDIR /var/www/html

# ---- 3. Dependencies (cached until composer.json / composer.lock change)
# Only the two manifest files are copied here - NOT the app code - so this
# expensive layer stays cached when only application code changes.
#   --no-scripts / --no-autoloader : artisan and app/ do not exist yet;
#                                    the autoloader is built in step 5.
COPY composer.json composer.lock ./
RUN composer install \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --no-autoloader

# ---- 4. Application code (this layer changes on every code edit) -----
COPY . .

# ---- 5. Finish the build (fast steps) --------------------------------
#   - dump-autoload (runs `artisan package:discover` via composer.json)
#   - make sure Laravel's writable dirs exist and belong to www-data
#   - strip Windows CRLF from the entrypoint so it runs on Linux
RUN composer dump-autoload --optimize \
 && mkdir -p storage/logs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache database \
 && chmod -R ug+rwX storage bootstrap/cache database \
 && sed -i 's/\r$//' docker/entrypoint.sh \
 && chmod +x docker/entrypoint.sh

# ---- Runtime configuration (no .env in the image; override with -e) --
# LOG_CHANNEL=stderr -> errors show up in `docker logs`.
# SQLite keeps the demo self-contained (no separate DB container needed).
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite

USER www-data

EXPOSE 8000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

# --host=0.0.0.0 is REQUIRED: the default (127.0.0.1) only listens inside
# the container, so the published port would be unreachable from outside.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
