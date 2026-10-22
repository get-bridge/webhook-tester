##############################################
# Stage 1: Install node dependencies and run gulp
##############################################

FROM node:11 AS npm
WORKDIR /app

COPY package.json /app
COPY package-lock.json /app
RUN npm install

COPY resources /app/resources
COPY gulpfile.js /app
RUN npm run gulp

##############################################
# Stage 2: Composer, nginx and fpm
##############################################

FROM php:8.4-fpm-trixie
WORKDIR /var/www/html

# 8.4.27 carries the fixes for CVE-2026-109618 and CVE-2026-109619; fail the build on anything older.
RUN php -r 'if (version_compare(PHP_VERSION, "8.4.27", "<")) { fwrite(STDERR, "PHP ".PHP_VERSION." is older than 8.4.27 and lacks the CVE-2026-109618/109619 fixes; rebuild with --pull once the base image is updated.\n"); exit(1); }' \
    && apt-get update \
    && apt-get install -y --no-install-recommends nginx unzip \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-install opcache pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Contains laravel echo server proxy configuration
COPY /nginx.conf /etc/nginx/conf.d

COPY /docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

USER www-data

COPY --chown=www-data:www-data /composer.json /var/www/html
COPY --chown=www-data:www-data /composer.lock /var/www/html

RUN composer install --no-interaction --no-autoloader --no-dev --prefer-dist --no-scripts \
    && composer clear-cache

COPY --chown=www-data:www-data /storage /var/www/html/storage
COPY --chown=www-data:www-data /bootstrap /var/www/html/bootstrap
COPY --chown=www-data:www-data /public /var/www/html/public
COPY --chown=www-data:www-data /artisan /var/www/html
COPY --chown=www-data:www-data /database /var/www/html/database
COPY --chown=www-data:www-data /config /var/www/html/config
COPY --chown=www-data:www-data /routes /var/www/html/routes
COPY --chown=www-data:www-data /app /var/www/html/app
COPY --chown=www-data:www-data /resources /var/www/html/resources

# Config is not cached: it reads the deployment's environment variables at runtime.
RUN composer dump-autoload --optimize --no-dev \
    && touch /var/www/html/database/database.sqlite \
    && php artisan route:cache \
    && php artisan event:cache \
    && php artisan migrate --force

COPY --chown=www-data:www-data --from=npm /app/public/css /var/www/html/public/css
COPY --chown=www-data:www-data --from=npm /app/public/js /var/www/html/public/js

USER root

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "queue:work", "--tries=3", "--timeout=10"]
