FROM php:8.2.28-cli-alpine AS builder

RUN apk add --no-cache \
        git \
        unzip \
        $PHPIZE_DEPS \
 && docker-php-ext-install pdo_mysql \
 && apk del $PHPIZE_DEPS \
 && rm -rf /var/cache/apk/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_HOME=/tmp/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --no-autoloader \
        --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev \
 && mkdir -p storage/logs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
 && sed -i 's/\r$//' docker/entrypoint.sh \
 && chmod +x docker/entrypoint.sh

FROM php:8.2.28-cli-alpine AS runtime

RUN addgroup -g 82 -S www-data 2>/dev/null || true \
 && adduser -u 82 -D -S -G www-data www-data 2>/dev/null || true

# Runtime deps: pdo_mysql + curl for HEALTHCHECK
RUN apk add --no-cache \
        curl \
        $PHPIZE_DEPS \
 && docker-php-ext-install pdo_mysql \
 && apk del $PHPIZE_DEPS \
 && rm -rf /var/cache/apk/*

WORKDIR /var/www/html


COPY --from=builder --chown=www-data:www-data /app /var/www/html

RUN mkdir -p storage/logs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
             database \
 && chown -R www-data:www-data storage bootstrap/cache database \
 && chmod -R ug+rwX storage bootstrap/cache database

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite


USER www-data

EXPOSE 8000


HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -f http://localhost:8000/ || exit 1

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
