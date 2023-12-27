FROM php:8.1-fpm
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/html
COPY . /var/www/html
COPY .env.example /var/www/html/.env
RUN composer install
RUN php artisan key:generate
EXPOSE 9000
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
