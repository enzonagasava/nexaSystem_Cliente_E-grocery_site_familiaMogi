FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    unzip git curl gnupg zip libzip-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql zip mbstring \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . .

RUN git config --global --add safe.directory /var/www/html

RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY laravel.conf /etc/apache2/sites-available/laravel.conf
RUN a2dissite 000-default.conf && a2ensite laravel.conf

EXPOSE 80
EXPOSE 5177

CMD ["apache2-foreground"]
