FROM php:7.3-fpm

COPY --from=composer:1.7.3 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN buildDeps=" \
        unzip \
    " \
    runtimeDeps=" \
        libicu-dev \
        libxml2-dev \
        libzip-dev \
    " \
    && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && docker-php-ext-install iconv intl mbstring opcache sockets zip

WORKDIR /var/www/html

COPY ["docker/docker-entrypoint.sh", "composer.json", "composer.lock", "./"]
COPY "src" "./src"
COPY "public" "./public"
COPY "config" "./config"
COPY "bin" "./bin"

RUN composer install --no-dev -o --prefer-dist -n

RUN apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/*

RUN chmod +x ./bin/dealdoh docker-entrypoint.sh

ENTRYPOINT ["/bin/bash", "docker-entrypoint.sh", "php-fpm"]

EXPOSE 9000
