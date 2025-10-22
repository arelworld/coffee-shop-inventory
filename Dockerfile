# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (important for Laravel routes)
RUN a2enmod rewrite

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies and generate app key
RUN composer install && php artisan key:generate

# Give Laravel storage permission
RUN chmod -R 777 storage bootstrap/cache

# Expose port 80 and start Apache
EXPOSE 80
CMD ["apache2-foreground"]
