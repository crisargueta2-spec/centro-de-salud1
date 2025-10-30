# Usa PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para URLs limpias
RUN a2enmod rewrite headers

# Copiar todo el contenido del repositorio al servidor
COPY . /var/www/html/

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Exponer el puerto 10000 (Render usa este por defecto)
EXPOSE 10000

# Comando de inicio
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html"]
