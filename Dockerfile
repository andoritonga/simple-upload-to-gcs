FROM php:8.1-apache
WORKDIR /
RUN apt-get update && apt-get install -y git
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite
RUN apt-get install -y wget

COPY . . /var/www/html/