FROM php:7.4.0RC4-fpm-alpine

ARG USER_ID

# System update
RUN apk update && apk upgrade

# Install system dependencies
RUN apk add --no-cache git                  \
                       bash                 \
                       nano                 \
                       curl                 \
                       unzip                \
                       make                 \
                       zip                  \
                       libzip-dev           \
                       $PHPIZE_DEPS


# Install php dependencies
RUN pecl install xdebug-beta
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create user
RUN addgroup --gid "$USER_ID" "dev" \
    && adduser \
    --disabled-password \
    --gecos "" \
    --ingroup "dev" \
    --uid "$USER_ID" \
    "dev"

WORKDIR /var/www