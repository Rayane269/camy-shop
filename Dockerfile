FROM php:8.3-fpm

# Installation des dépendances système + Node.js (nécessaire pour Vite)
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip curl nginx \
    libicu-dev libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring opcache gd intl zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Installation des dépendances PHP et compilation des assets Vite (CSS/JS)
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build

# Permission sur les dossiers de stockage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD php artisan config:clear && php artisan route:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80