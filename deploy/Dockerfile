# Development stage
FROM php:8.3-fpm-alpine as development

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    redis \
    nodejs \
    npm \
    oniguruma-dev \
    icu-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader || true
RUN npm install || true

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

# Production stage
FROM php:8.3-fpm-alpine as production

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    redis \
    nodejs \
    npm \
    oniguruma-dev \
    icu-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "opcache.enable=1" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/php.ini" && \
    echo "opcache.validate_timestamps=0" >> "$PHP_INI_DIR/php.ini"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
