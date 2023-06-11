FROM composer:2.5.5 as composerDocker

FROM php:8.1.14-cli-buster

WORKDIR /application

ENV DEBIAN_FRONTEND noninteractive

RUN apt update && \
    apt install -y git && \
    apt install -y zip unzip libzip-dev && \
    pecl install xdebug-3.1.2 && \
    docker-php-ext-configure zip --with-zip && \
    docker-php-ext-install zip bcmath && \
    docker-php-ext-enable xdebug

ADD ./docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composerDocker /usr/bin/composer /usr/bin/composer

COPY . /application

ARG COMPOSER_AUTH

RUN composer install --prefer-dist
