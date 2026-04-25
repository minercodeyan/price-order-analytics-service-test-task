FROM php:8.2-apache

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    libxml2-dev \
    libonig-dev \
    libsodium-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP расширения (только нужные)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    bcmath \
    mbstring \
    soap \
    intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache
RUN a2enmod rewrite

# Пользователь
RUN groupadd -g ${GID} laravel \
    && useradd -u ${UID} -g laravel -m laravel \
    && usermod -aG www-data laravel

WORKDIR /var/www/html

COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

USER laravel

EXPOSE 80

CMD ["apache2-foreground"]