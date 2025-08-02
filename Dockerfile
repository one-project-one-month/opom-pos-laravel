FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    npm nodejs \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring xml bcmath intl

# Set working directory
WORKDIR /var/www

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only necessary files for composer install
COPY . .

# Install Laravel dependencies (no dev dependencies for production)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy custom php config
COPY docker/php.ini /usr/local/etc/php/php.ini

# Generate application key (only if .env doesn't exist)
RUN if [ ! -f .env ]; then cp .env.example .env && php artisan key:generate; fi

# Install Node & build assets
RUN npm install && npm run build

# Set correct permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

VOLUME /var/www

EXPOSE 9000
CMD ["php-fpm"]
