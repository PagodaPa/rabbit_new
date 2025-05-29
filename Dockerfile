# базовый образ PHP с FPM
FROM php:8.2-fpm

# Установка системных утилит и зависимостей
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    librabbitmq-dev \
    libonig-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install pdo_mysql zip mbstring sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install sockets

# Установка phpStan
RUN composer global require phpstan/phpstan

# Добавляем в PATH глобальные пакеты
ENV PATH="/root/.composer/vendor/bin:${PATH}"

WORKDIR /var/www/html