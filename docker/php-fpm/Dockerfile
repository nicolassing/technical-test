FROM php:8-fpm

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    libzip-dev \
    libicu-dev
RUN docker-php-ext-install \
    zip \
    pdo_mysql

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
    && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
    && php composer-setup.php && rm composer-setup.php* \
    && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/app
