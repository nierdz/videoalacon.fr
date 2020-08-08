FROM php:7.4-fpm
ENV HOME_DIR /var/www
SHELL ["/bin/bash", "-o", "errexit", "-o", "pipefail", "-o", "nounset", "-c"]
# hadolint ignore=DL3022
COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer

# hadolint ignore=DL3008
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      less \
      libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install zip

WORKDIR $HOME_DIR

COPY ./composer.json composer.json
COPY ./composer.lock composer.lock
RUN composer install -n \
    && chown -R www-data:www-data /var/www/
COPY ./.env .env
COPY ./config config

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp
COPY ./wp-cli.yml wp-cli.yml
