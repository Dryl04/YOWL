# Étape 1 : Image de base PHP avec extensions nécessaires
FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Créer le répertoire de travail
WORKDIR /var/www

# Copier les fichiers Laravel
COPY . .

# Installer les dépendances PHP
RUN composer install --no-interaction --no-plugins --no-scripts

# Donner les permissions correctes
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

# Lancer PHP-FPM
CMD ["php-fpm"]
