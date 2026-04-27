FROM php:8.2-fpm


RUN sed -i 's/deb.debian.org/mirror.yandex.ru/g' /etc/apt/sources.list.d/debian.sources || \
    sed -i 's/deb.debian.org/mirror.yandex.ru/g' /etc/apt/sources.list && \
    apt-get update && \
    apt-get install -y --no-install-recommends git unzip && \
    docker-php-ext-install sockets && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html


COPY composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

CMD ["php-fpm"]
