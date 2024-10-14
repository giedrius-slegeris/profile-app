FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install -y git curl wget nano zip unzip libpng-dev libonig-dev libxml2-dev zlib1g-dev \
    libssl-dev libicu-dev libjpeg-dev libfreetype6-dev nodejs npm
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache intl

RUN pecl install apcu protobuf grpc
RUN docker-php-ext-enable apcu protobuf grpc

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install -y symfony-cli

WORKDIR /app
RUN npm install bootstrap@^5 jquery@^3

# add below when deploying
# RUN composer install -n

CMD ["apache2-foreground"]