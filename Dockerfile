FROM php:8.3-cli

# Install system dependencies, build tools for dlib/OpenCV/grpc, PHP extensions, and Python 3
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
    linux-headers-generic \
    python3 \
    python3-pip \
    python3-venv \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Python dependencies if requirements.txt exists
RUN if [ -f requirements.txt ]; then pip3 install --no-cache-dir --break-system-packages -r requirements.txt; fi

# Ensure permissions and storage link
RUN php artisan storage:link || true \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan config:clear && php artisan migrate --force && (php artisan queue:work --sleep=3 --tries=3 &) && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
