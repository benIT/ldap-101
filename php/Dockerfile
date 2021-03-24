FROM php:7.4-fpm
RUN \
    apt-get update && \
    apt-get install git libzip-dev zip unzip libldap2-dev -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer