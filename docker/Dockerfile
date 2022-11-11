FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt update && apt install -y \
    zlib1g-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git \
    curl \
    #libmagickwand-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libcurl3-dev \
    libfontconfig1 \
    libxrender1 \
    zip \
    ca-certificates \
    vim \
    unzip && \
    apt clean
    #&& rm -rf /var/lib/apt/lists/*

# Install PHP extensions and configure php composer
RUN docker-php-ext-configure gd \
    --with-jpeg=/usr/include/ \
    --with-freetype=/usr/include && \
    docker-php-ext-install curl pdo_pgsql mbstring exif pcntl bcmath gd && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug
    #&& pecl install imagick; \
    #&& docker-php-ext-enable imagick

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    chown -R $user:www-data /var/www

WORKDIR /var/www

USER $user
