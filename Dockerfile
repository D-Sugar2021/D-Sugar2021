FROM composer:2 as vendor
COPY . .
RUN composer install --no-dev --no-interaction --no-plugins --no-scripts

FROM php:8.1-fpm-alpine
COPY --from=vendor /app/vendor/ /app/vendor/
COPY . /app
WORKDIR /app
RUN chown -R www-data:www-data /app
CMD ["php-fpm"]
