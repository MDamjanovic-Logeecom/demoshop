# Base container with apache pre-installed
FROM php:8.4-apache

# Working directory set inside the apache container
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    git \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Enables Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite

# Copy project files into the container
COPY . /var/www/html

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

# Set permissions for default apache folder
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy startup script into image
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80 (HTTP)
EXPOSE 80

# Use entrypoint script for automatic setup
ENTRYPOINT ["docker-entrypoint.sh"]