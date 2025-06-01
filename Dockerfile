FROM php:8.3-fpm-alpine

# Install PHP extensions
RUN apk add --no-cache \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install gd pdo pdo_mysql zip mbstring bcmath

# ✅ Custom PHP configs for large upload
RUN echo "upload_max_filesize=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "post_max_size=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "memory_limit=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "file_uploads=On" >> /usr/local/etc/php/conf.d/custom-php.ini

WORKDIR /var/www/app

CMD ["php-fpm"]
