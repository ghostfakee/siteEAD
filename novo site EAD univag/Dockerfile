FROM php:8.2-apache

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql mysqli && \
    a2enmod rewrite

COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
