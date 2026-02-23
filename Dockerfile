FROM php:8.2-fpm

# Argumenty budowania
ARG user=laravel
ARG uid=1000

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor

# Czyszczenie cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalacja rozszerzeń PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

# Instalacja Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Pobranie najnowszego Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Utworzenie użytkownika systemowego
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Ustawienie katalogu roboczego
WORKDIR /var/www

# Kopiowanie plików konfiguracyjnych
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Kopiowanie skryptu entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 dla PHP-FPM
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
