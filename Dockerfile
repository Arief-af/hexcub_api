FROM php:8-fpm-alpine

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
    curl \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install gd pdo pdo_mysql zip mbstring bcmath

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy file composer ke container
COPY composer.json composer.lock /var/www/app/

WORKDIR /var/www/app

RUN composer install --no-dev --optimize-autoloader

RUN echo "upload_max_filesize=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "post_max_size=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "memory_limit=4096M" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom-php.ini \
 && echo "file_uploads=On" >> /usr/local/etc/php/conf.d/custom-php.ini

# Copy seluruh kode aplikasi setelah install composer supaya cache docker lebih efisien
COPY . /var/www/app

CMD ["php-fpm"]
