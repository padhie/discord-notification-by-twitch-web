# https://github.com/nanoninja/php-fpm
FROM php:8.2-fpm
WORKDIR "/application"

# Install basics
RUN apt-get update
RUN apt-get -y --no-install-recommends install sudo
RUN apt-get -y --no-install-recommends install nano
RUN apt-get -y --no-install-recommends install htop
RUN apt-get -y --no-install-recommends install iputils-ping
RUN apt-get -y --no-install-recommends install wget
RUN apt-get -y --no-install-recommends install curl
RUN apt-get -y --no-install-recommends install zip
RUN apt-get -y --no-install-recommends install unzip
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# set aliases
RUN echo 'alias l="ls -a"' >> ~/.bashrc
RUN echo 'alias ll="ls -l"' >> ~/.bashrc
RUN echo 'alias la="ls -la"' >> ~/.bashrc

# Install selected extensions and other stuff
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN apt-get update
RUN apt-get -y --no-install-recommends install default-mysql-client
RUN apt-get -y --no-install-recommends install zlib1g-dev
RUN apt-get -y --no-install-recommends install libicu-dev
RUN apt-get -y --no-install-recommends install g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install opcache

# Install php settings
# RUN apt-get install software-properties-common
RUN ln -s /application/tools/docker/php8.2/php.ini /usr/local/etc/php/conf.d/php.ini
RUN ln -s /application/tools/docker/php8.2/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install xdebug
# https://stackoverflow.com/questions/23408771/xdebug-error-failed-loading-xdebug-so-xdebug-so-cannot-open-shared-object-fi
RUN yes | pecl install xdebug
RUN ln -sf /application/tools/docker/php8.2/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer