FROM php:8.0-fpm
LABEL version=1.0.0
SHELL ["/bin/bash", "-o", "errexit", "-o", "pipefail", "-o", "nounset", "-c"]
# hadolint ignore=DL3022
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# hadolint ignore=DL3009,DL3008
RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
    ca-certificates \
    gnupg1
RUN curl -o nginx_signing.key https://nginx.org/keys/nginx_signing.key && \
    apt-key add nginx_signing.key && \
    echo "deb https://nginx.org/packages/mainline/debian/ bullseye nginx" > /etc/apt/sources.list.d/nginx.list

# hadolint ignore=DL3008
RUN apt-get update \
  && apt-get install -y --no-install-recommends --no-install-suggests \
    git \
    less \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    nginx \
    supervisor \
    unzip \
  && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
  && docker-php-ext-install -j "$(nproc)" \
    bcmath \
    exif \
    gd \
    intl \
    mysqli \
    opcache \
    zip \
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  && rm -rf /var/lib/apt/lists/* /tmp/*

RUN mkdir /var/www/bedrock
WORKDIR /var/www/bedrock
RUN composer create-project --no-dev --no-scripts roots/bedrock /var/www/bedrock 1.21.1 && \
    composer require --update-no-dev roots/wordpress:6.1.1 && \
    composer require --update-no-dev wpackagist-plugin/flush-opcache:4.1.4 && \
    chown -R www-data:www-data /var/www/bedrock
COPY .env .env
COPY config/environments config/environments/

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp
COPY wp-cli.yml wp-cli.yml

COPY madrabbit /var/www/bedrock/web/app/themes/madrabbit
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisor/nginx.conf /etc/supervisor/conf.d/nginx.conf
COPY supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
COPY php/zzz-hardening.ini /usr/local/etc/php/conf.d/zzz-hardening.ini
COPY php/zzz-tuning.ini /usr/local/etc/php/conf.d/zzz-tuning.ini
COPY php/zzz-opcache.ini /usr/local/etc/php/conf.d/zzz-opcache.ini

RUN mkdir /docker-entrypoint.d
COPY scripts/docker-entrypoint.sh /
COPY scripts/wordpress.sh /docker-entrypoint.d
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
