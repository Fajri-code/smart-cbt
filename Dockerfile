FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    unzip \
    git \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable opcache \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader \
    && npm ci --ignore-scripts

COPY . .
RUN npm run build

EXPOSE 8000

RUN printf '%s\n' \
    'opcache.enable=1' \
    'opcache.enable_cli=1' \
    'opcache.validate_timestamps=1' \
    'opcache.revalidate_freq=0' \
    'opcache.memory_consumption=128' \
    'opcache.interned_strings_buffer=16' \
    'opcache.max_accelerated_files=20000' \
    > /usr/local/etc/php/conf.d/opcache-performance.ini

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
