FROM php:7.4-fpm
SHELL ["/bin/bash", "-o", "errexit", "-o", "pipefail", "-o", "nounset", "-c"]
# hadolint ignore=DL3022
COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer

# hadolint ignore=DL3008
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      less \
      libzip-dev \
      unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

RUN docker-php-ext-install mysqli \
    && docker-php-ext-install zip

RUN mkdir /var/www/bedrock
WORKDIR /var/www/bedrock
RUN composer create-project roots/bedrock /var/www/bedrock 1.14.2 \
    && chown -R www-data:www-data /var/www/bedrock
COPY ./.env .env
COPY ./config/environments config/environments/

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp
COPY ./wp-cli.yml wp-cli.yml
COPY ./scripts/entrypoint.sh /usr/local/bin/docker-php-entrypoint
