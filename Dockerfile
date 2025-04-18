FROM php:8.3-fpm
LABEL version=1.6.0
SHELL ["/bin/bash", "-o", "errexit", "-o", "pipefail", "-o", "nounset", "-c"]
# hadolint ignore=DL3022
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV \
  APP_DIR=/var/www/bedrock \
  BEDROCK_VERSION=1.26.1 \
  WORDPRESS_VERSION=6.7.2 \
  WP_OPCACHE_VERSION=4.2.3

WORKDIR ${APP_DIR}

# hadolint ignore=DL3009,DL3008
RUN apt-get update \
  && apt-get install -y --no-install-recommends --no-install-suggests \
    ca-certificates \
    git \
    gnupg1 \
    gpg \
    jq \
    less \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
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
    pdo_mysql \
    zip \
  # TODO activate this shit again when fixed
  #&& pecl install imagick \
  #&& docker-php-ext-enable imagick \
  && pecl install igbinary \
  && docker-php-ext-enable igbinary \
  && curl -sS https://nginx.org/keys/nginx_signing.key | /usr/bin/gpg --dearmor | /usr/bin/tee /etc/apt/trusted.gpg.d/nginx.gpg \
  && echo "deb https://nginx.org/packages/mainline/debian/ bookworm nginx" > /etc/apt/sources.list.d/nginx.list \
  && apt-get update \
  && apt-get install -y --no-install-recommends --no-install-suggests \
    ffmpeg \
    nginx \
    supervisor \
  && composer create-project --no-dev --no-scripts roots/bedrock . ${BEDROCK_VERSION} \
  && composer require --update-no-dev roots/wordpress:${WORDPRESS_VERSION} \
  && composer require --update-no-dev wpackagist-plugin/flush-opcache:${WP_OPCACHE_VERSION} \
  && composer require --update-no-dev abraham/twitteroauth \
  && composer update \
  && curl -o /usr/bin/wp -L https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x /usr/bin/wp \
  && curl -o /usr/bin/yt-dlp -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp_linux \
  && chmod +x /usr/bin/yt-dlp \
  && rm -rf \
    /var/lib/apt/lists/* \
    /usr/src/* \
    ${APP_DIR}/web/app/themes/twentytwentyfive

COPY valc ${APP_DIR}/web/app/themes/valc
COPY config/production.php ${APP_DIR}/config/environments/production.php
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisor/nginx.conf /etc/supervisor/conf.d/nginx.conf
COPY supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf

COPY scripts/docker-entrypoint.sh /
COPY scripts/media_importer.sh /usr/bin/media_importer
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
