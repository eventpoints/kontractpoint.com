FROM ghcr.io/eventpoints/php:main AS php

ENV APP_ENV="prod" \
    APP_DEBUG=0 \
    PHP_OPCACHE_PRELOAD="/app/config/preload.php" \
    PHP_EXPOSE_PHP="off" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

RUN rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN mkdir -p var/cache var/log

# Intentionally split into multiple steps to leverage docker layer caching
COPY composer.json composer.lock symfony.lock ./

RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts

# Install npm packages
COPY importmap.php ./
# Production yarn build
COPY ./assets ./assets

RUN php bin/console importmap:install
RUN php bin/console tailwind:build

COPY . .

# Need to run again to trigger scripts with application code present
RUN composer install --no-dev --no-interaction --classmap-authoritative
RUN composer symfony:dump-env prod
RUN chmod -R 777 var

FROM ghcr.io/eventpoints/caddy:main AS caddy

COPY --from=php /app/public public/