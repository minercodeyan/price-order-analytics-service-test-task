FROM php:8.2-fpm

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
    libicu-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    bcmath \
    mbstring \
    soap \
    intl \
    opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаём пользователя
RUN groupadd -g ${GID} laravel \
    && useradd -u ${UID} -g laravel -m laravel \
    && usermod -aG www-data laravel

WORKDIR /var/www/html

USER laravel

CMD ["php-fpm"]
