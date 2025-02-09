FROM php:8.2-cli

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app/.

# Ensure necessary directories exist
RUN mkdir -p /var/log/nginx && mkdir -p /var/cache/nginx

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear Symfony cache
RUN php bin/console cache:clear --no-warmup --env=prod

# Expose the application's port
EXPOSE 8000

# Start the Symfony server (for development, consider using Nginx/Apache in production)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
