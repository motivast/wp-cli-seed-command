# Use PHP_VERSION arg to change php version in CI
ARG PHP_VERSION=7.3

FROM php:${PHP_VERSION}-fpm

# Install MariaDB extension
RUN apt-get update \
	&& apt-get install -y mariadb-client \
    && docker-php-ext-install mysqli

# Install ImageMagick extension
RUN apt-get update \
	&& apt-get install -y libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install utils
RUN apt-get update \
	&& apt-get install -y git subversion zip unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install WP-CLI
RUN curl -sLO https://github.com/wp-cli/wp-cli/releases/download/v2.3.0/wp-cli-2.3.0.phar \
    && chmod +x wp-cli-2.3.0.phar \
	&& mv wp-cli-2.3.0.phar /usr/local/bin/wp

# Install Node
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
	&& apt-get install -y nodejs

# Use the default development configuration
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
