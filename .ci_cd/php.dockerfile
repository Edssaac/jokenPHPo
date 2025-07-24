FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libpcre3-dev \
    zlib1g-dev \
    libxml2-dev \
    libbrotli-dev \
    build-essential \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && a2enmod rewrite \
    && sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]