FROM php:8.3-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo_mysql

COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache/.htaccess /var/www/html/public/.htaccess

