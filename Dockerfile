FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ libicu-dev libzip-dev zip \
    && docker-php-ext-install intl opcache pdo_mysql
#    && pecl install apcu \
#    && docker-php-ext-enable apcu \
#    && docker-php-ext-configure intl \
#    && docker-php-ext-install intl \
#    && docker-php-ext-configure zip \
#    && docker-php-ext-install zip

WORKDIR /var/www/project

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer | bash \
    && php -r "unlink('composer.phar');"

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli -y