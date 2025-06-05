FROM php:8.3-fpm-alpine

WORKDIR /var/www/app

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev libzip-dev oniguruma-dev bash curl \
    && apk add --no-cache libpng libjpeg-turbo libwebp libxpm libzip oniguruma zip unzip \
    && docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-webp=/usr/include/ --with-xpm=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip mbstring bcmath \
    && apk del .build-deps

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy semua kode aplikasi sekaligus
COPY . .

ENV COMPOSER_DISABLE_POST_AUTOLOAD_DUMP=1

RUN composer install --no-dev --optimize-autoloader

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/app

ENV COMPOSER_DISABLE_POST_AUTOLOAD_DUMP=

CMD ["php-fpm"]
