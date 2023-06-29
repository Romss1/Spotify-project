FROM php:8.2-fpm

LABEL version=v1.0.0
LABEL owner=romain
ARG APP_USER=romain

RUN adduser --no-create-home ${APP_USER}

RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu xdebug && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium xdebug && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY xdebug.ini "${PHP_INI_DIR}/conf.d/xdebug.ini"
COPY error_reporting.ini "${PHP_INI_DIR}/conf.d/error_reporting.ini"

USER ${APP_USER}:${APP_USER}
COPY --chown=${APP_USER} --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www