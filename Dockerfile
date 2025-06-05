FROM php:8.3-fpm-alpine

# Set working directory early
WORKDIR /var/www/app

# Install dependencies and build tools
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
        oniguruma-dev \
        bash \
        curl \
    && apk add --no-cache \
        zip \
        unzip \
    # Configure and install PHP extensions
    && docker-php-ext-configure gd \
        --with-jpeg=/usr/include/ \
        --with-webp=/usr/include/ \
        --with-xpm=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mbstring bcmath \
    # Clean up build dependencies
    && apk del .build-deps

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy only composer files first for caching
COPY composer.json composer.lock ./

# Install composer dependencies (without dev)
RUN composer install --no-dev --optimize-autoloader

# Copy rest of the application code
COPY . .

# Set php.ini custom settings
RUN echo "upload_max_filesize=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "post_max_size=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "memory_limit=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "file_uploads=On" >> /usr/local/etc/php/conf.d/custom-php.ini

# Set permissions for www-data user (default user of php-fpm)
RUN chown -R www-data:www-data /var/www/app

# Use php-fpm as entrypoint
CMD ["php-fpm"]