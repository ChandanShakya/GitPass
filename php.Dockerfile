FROM php:7.3.32-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql
