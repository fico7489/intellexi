FROM php:8.2.16-fpm-alpine3.19

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

COPY --from=composer:2.7.1 /usr/bin/composer /usr/local/bin/composer

#set www-data user
RUN sed -ri 's/^www-data:x:82:82:/www-data:x:1000:1000:/' /etc/passwd

USER www-data
