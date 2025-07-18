# Use a base image with both PHP and Node.js
FROM anothrnick/php-caddy:8.2-fpm as builder

# Install system dependencies
RUN apk add --no-cache nodejs npm

WORKDIR /app

# Copy all application files
COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build frontend
RUN npm install
RUN npm run build

# Run optimizations for production
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache


# Final production image with Caddy Web Server
FROM caddy:2-alpine
WORKDIR /var/www/html
COPY --from=builder /app .
COPY Caddyfile /etc/caddy/Caddyfile
