# Use PHP 8.1 with Apache
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy application code
COPY app/ .

# Install Composer dependencies if composer.json exists
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create runtime directories for Yii2
RUN mkdir -p runtime web/assets \
    && chmod 777 runtime web/assets

EXPOSE 80

CMD ["apache2-foreground"]