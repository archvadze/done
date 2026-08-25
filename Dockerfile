# --- Stage 1: install PHP dependencies ---
FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# --- Stage 2: build frontend assets (Vite/Tailwind) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY --from=vendor /app ./
RUN npm install && npm run build

# --- Stage 3: runtime image ---
FROM php:8.2-cli
RUN apt-get update && apt-get install -y \
      libsqlite3-dev libzip-dev unzip git libpng-dev \
    && docker-php-ext-install pdo pdo_sqlite zip gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --from=assets /app ./

RUN mkdir -p database storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["/entrypoint.sh"]
