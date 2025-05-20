FROM php:8.2-apache

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Instalar Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar configuración personalizada de Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar configuración personalizada de PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Instalar herramientas útiles
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html