FROM php:8.3-cli

# 1. I-install ang system dependencies (PHP, Python, ug Node.js)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    cmake \
    build-essential \
    libgl1 \
    libglib2.0-0 \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev \
    libssl-dev \
    python3 \
    python3-pip \
    python3-venv \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Direktang kopyahin ang pre-compiled na gRPC (walang compilation)
COPY --from=ghcr.io/extphprs/grpc-php-rs:latest-php8.3 /usr/local/ /usr/local/

# 3. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 4. Copy project files
COPY . .

# 5. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Install Node dependencies ug i-build ang Vite assets
RUN npm install && npm run build

# 7. Install Python dependencies if requirements.txt exists
RUN if [ -f requirements.txt ]; then pip3 install --no-cache-dir --break-system-packages -r requirements.txt; fi

# 8. Ensure permissions and storage link
RUN php artisan storage:link || true \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# 9. Simplified CMD
CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
