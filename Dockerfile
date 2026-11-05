# PHP 8.2 with the Apache web server
FROM php:8.2-apache

# System tools + PHP extensions (zip and MongoDB)
RUN apt-get update && apt-get install -y git unzip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install zip \
    && pecl install mongodb-2.5.5 \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Node.js (to build the CSS and JavaScript with Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer (PHP package manager)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache serves the Laravel "public" folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .

# Install PHP packages and build the design files
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci \
    && npm run build \
    && rm -rf node_modules \
    && chown -R www-data:www-data storage bootstrap/cache

# On start: use Render's port, cache the config, then run Apache
CMD sh -c "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf \
    && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && apache2-foreground"