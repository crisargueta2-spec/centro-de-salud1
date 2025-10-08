# Imagen oficial con Apache + PHP 8.2
FROM php:8.2-apache

# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Activar módulos útiles de Apache
RUN a2enmod rewrite headers

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copiar código al DocumentRoot
COPY . /var/www/html/

# Variables de entorno (Railway pondrá DB_*). $PORT lo maneja Apache en 80.
EXPOSE 80
