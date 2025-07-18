# Stage 1: Build Frontend Assets
FROM node:18-alpine as frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Build PHP Application
FROM php:8.2-fpm-alpine as app
WORKDIR /var/www/html
RUN apk add --no-cache oniguruma-dev libxml2-dev libzip-dev
RUN docker-php-ext-install bcmath ctype fileinfo mbstring pdo pdo_mysql tokenizer xml zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN composer install --no-dev --optimize-autoloader

# Run optimizations for production
RUN php artisan key:generate --force
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Stage 3: Final Production Image with Caddy Web Server
FROM caddy:2-alpine
WORKDIR /var/w
