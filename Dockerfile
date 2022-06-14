FROM php:8.1-apache
WORKDIR /
RUN apt-get update && apt-get install -y git
RUN a2enmod rewrite
RUN apt-get install -y wget

COPY . /var/www/html/
