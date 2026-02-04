FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Copy custom Apache configuration
COPY ./.docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf

# Change ownership of the working directory
RUN chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]