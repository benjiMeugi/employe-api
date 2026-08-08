FROM composer:2 AS composer

FROM php:8.3-fpm-alpine

# Extensions système nécessaires à Laravel + MySQL
RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/employe-api

# Le code est monté en volume par docker-compose (voir docker-compose.yml).
# Rien à COPY ici : composer install se fait une fois, côté hôte, avant le
# premier "docker compose up" (voir le README pour la marche à suivre).

EXPOSE 9000
CMD ["php-fpm"]