# Stage 1: Install PHP dependencies with Composer
FROM composer:2 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Stage 2: Build frontend assets with Node.js
FROM node:18 as frontend
WORKDIR /app
COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm install
COPY . .
RUN npm run build

# Stage 3: Create the final production image
FROM anothrnick/php-caddy:8.2-fpm
WORKDIR /app
COPY --from=vendor /app/vendor/ /app/vendor/
COPY --from=frontend /app/public/build /app/public/build
COPY . .
