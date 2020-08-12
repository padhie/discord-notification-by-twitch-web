FROM php:7.4-fpm
WORKDIR "/application"

# Install basics
RUN apt-get update \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && apt-get install -y sudo nano iputils-ping \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# set aliases
RUN echo 'alias l="ls -a"' >> ~/.bashrc
RUN echo 'alias ll="ls -l"' >> ~/.bashrc
RUN echo 'alias la="ls -la"' >> ~/.bashrc

# Install xdebug
# https://stackoverflow.com/questions/23408771/xdebug-error-failed-loading-xdebug-so-xdebug-so-cannot-open-shared-object-fi
RUN yes | pecl install xdebug
RUN ln -s /application/tools/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
