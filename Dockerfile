FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    imagemagick \
    libmcrypt-dev \
    libpng-dev \
    libpq-dev \
    libxrender1 \
    locales \
    openssh-client \
    patch \
    unzip \
    zlib1g-dev \
    zip \
    --no-install-recommends && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    gd \
    bcmath \
    pcntl \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer