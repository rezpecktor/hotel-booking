# Gunakan base image yang sudah berisi PHP, Composer, dan Node.js
FROM aevitas/php-dev:8.2-cli

# Tentukan direktori kerja
WORKDIR /app

# Salin semua file proyek
COPY . .

# Install dependensi PHP (Composer)
RUN composer install --no-dev --optimize-autoloader

# Install dependensi JavaScript (NPM) dan build aset
RUN npm install
RUN npm run build

# Jalankan optimasi Laravel untuk produksi
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Atur izin folder
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Beri tahu port mana yang akan digunakan
EXPOSE 8080

# Perintah untuk menjalankan aplikasi
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
