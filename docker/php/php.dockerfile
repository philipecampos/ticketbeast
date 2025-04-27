# Etapa de build (intermediária)
FROM php:8.4-fpm AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libonig-dev \
    libpq-dev \
    libcurl3-dev \
    unzip \
    vim \
    && apt-get clean
#    libxml2-dev \
#    libfontconfig1 \
#    libxrender1 \
#    zip \
#    zlib1g-dev \
#    && rm -rf /var/lib/apt/lists/*

#=====
# Install PHP extensions
#=====
## Laravel's dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install curl pgsql pdo_pgsql pdo_mysql mbstring exif pcntl bcmath

#=====xdebug
RUN pecl install xdebug-3.4.2 \
    && docker-php-ext-enable xdebug
#=====xdebug

#=====PHPGD
#RUN apt install -y \
#    libpng-dev \
#    libfreetype6-dev \
#    libjpeg62-turbo-dev \
#  && docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
#  && docker-php-ext-install gd
#  && apt cache clear
#=====PHPGD

#=====Imagic
#RUN apt install -y libmagickwand-dev \
#    && pecl install imagick \
#    && docker-php-ext-enable imagick \
#    && apt clean
#=====Imagic

# Imagem final menor
FROM php:8.4-fpm

# Copiar apenas o necessário da imagem intermediária
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

# Create system user to run Composer and Artisan Commands
ARG user
ARG uid
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
RUN chown -R $user:www-data /var/www

WORKDIR /var/www

USER $user
