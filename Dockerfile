# --- Stage 1: Code Modifications & Frontend Compilation ---
FROM php:8.3-cli-alpine AS builder
WORKDIR /build

# Install git, Node.js, and npm
RUN apk add --no-cache git nodejs npm

# Clone repository down inside build container
RUN git clone https://github.com/picstome/picstome.git .

# Execute your exact automated Flux Pro strip and code modification rule
RUN php -r " \
    \$file = 'composer.json'; \
    \$data = json_decode(file_get_contents(\$file), true); \
    if (isset(\$data['require']['livewire/flux-pro'])) unset(\$data['require']['livewire/flux-pro']); \
    if (isset(\$data['repositories']['flux-pro'])) unset(\$data['repositories']['flux-pro']); \
    if (empty(\$data['repositories'])) unset(\$data['repositories']); \
    file_put_contents(\$file, json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); \
"

# Copy local model fixes to overwrite CDN issues
COPY app/Models/Team.php ./app/Models/Team.php
COPY app/Models/Photo.php ./app/Models/Photo.php
COPY app/Providers/AppServiceProvider.php ./app/Providers/AppServiceProvider.php
COPY resources/views/components/flux/ ./resources/views/components/flux/

# Bring in Composer and install PHP dependencies first so the vendor folder exists
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN rm -f composer.lock && composer install --ignore-platform-reqs --no-scripts --no-interaction

# Now that vendor/livewire/flux exists, compile the frontend assets
RUN npm install && npm run build

# --- Stage 2: Final Multi-Process Engine Appliance ---
FROM php:8.3-cli-alpine
WORKDIR /app

# Install runtime extensions and Supervisor manager
RUN apk add --no-cache \
    bash curl supervisor sqlite sqlite-dev unzip libpng-dev libjpeg-turbo-dev freetype-dev linux-headers gcompat \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite pcntl bcmath exif sockets

# Grab Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Standalone MinIO Server engine & client binaries dynamically based on CPU architecture
RUN ARCH=$(uname -m) && \
    if [ "$ARCH" = "aarch64" ] || [ "$ARCH" = "arm64" ]; then \
        MINIO_ARCH="arm64"; \
    else \
        MINIO_ARCH="amd64"; \
    fi && \
    curl -fL -o /usr/local/bin/minio "https://dl.min.io/server/minio/release/linux-${MINIO_ARCH}/minio" && \
    curl -fL -o /usr/local/bin/minio-client "https://dl.min.io/client/mc/release/linux-${MINIO_ARCH}/mc" && \
    chmod +x /usr/local/bin/minio /usr/local/bin/minio-client

# Install Standalone Mailpit
RUN curl -sL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh | bash

# Copy clean pre-built source directory from Stage 1
COPY --from=builder /build /app

# Conditionally create .env, patch the CDN domain, and install Composer dependencies
RUN if [ ! -f .env ]; then cp .env.example .env; fi \
    && sed -i 's|PICSTOME_PHOTO_CDN_DOMAIN=.*|PICSTOME_PHOTO_CDN_DOMAIN=|g' .env \
    && rm -f composer.lock \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Copy configurations and script entrypoints
COPY supervisord.conf /app/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose combined internal ports
# 8000: Web Application | 9000: S3 Engine API | 9001: S3 GUI Console | 8025: Mailpit SMTP GUI
EXPOSE 8000 9000 9001 8025

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
