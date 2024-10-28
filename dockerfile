# Utiliza la imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instala Composer y algunas dependencias necesarias
RUN apt-get update && \
    apt-get install -y git unzip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto 80 para Apache
EXPOSE 80

# Activa el módulo de reescritura de Apache
RUN a2enmod rewrite

# Copia los archivos desde la carpeta de tu host a la imagen en el contenedor
COPY . /var/www/html

# Da permisos a la carpeta para que sea accesible y modificable
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Aumenta la memoria
RUN echo "memory_limit = 128M" > /usr/local/etc/php/conf.d/memory-limit.ini