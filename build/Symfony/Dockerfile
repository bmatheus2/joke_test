FROM php:7.4-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
      bash \
      git \
      php-sqlite3 \
    && wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && mkdir /srv/app

WORKDIR /srv/app

CMD ["symfony", "server:start", "--no-tls"]
