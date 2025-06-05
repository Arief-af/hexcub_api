FROM php:8.3-fpm-alpine

WORKDIR /var/www/app

# Install dependencies dan build tools
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
        libpng \
        libjpeg-turbo \
        libwebp \
        libxpm \
        libzip \
        oniguruma \
        zip \
        unzip \
    && docker-php-ext-configure gd \
        --with-jpeg=/usr/include/ \
        --with-webp=/usr/include/ \
        --with-xpm=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mbstring bcmath \
    && apk del .build-deps

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy seluruh source code sekaligus (termasuk artisan)
COPY . .

# Disable post-autoload-dump sementara supaya tidak gagal saat artisan dipanggil
ENV COMPOSER_DISABLE_POST_AUTOLOAD_DUMP=1

# Install composer dependencies tanpa dev dan optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Jalankan post-autoload-dump secara manual setelah kode lengkap ada
RUN composer dump-autoload --optimize

# Set php.ini custom settings
RUN echo "upload_max_filesize=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "post_max_size=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "memory_limit=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "file_uploads=On" >> /usr/local/etc/php/conf.d/custom-php.ini

# Set permission folder aplikasi ke www-data
RUN chown -R www-data:www-data /var/www/app

# Reset environment variable supaya tidak mengganggu runtime container
ENV COMPOSER_DISABLE_POST_AUTOLOAD_DUMP=

CMD ["php-fpm"]
