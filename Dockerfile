FROM php:8.3-cli

# Install system dependencies, SQLite, and Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions for Laravel/Picstome
RUN docker-php-ext-install pdo_sqlite pcntl bcmath exif sockets

# Grab the latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the initialization script and make it executable
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Run the script on container start
ENTRYPOINT ["entrypoint.sh"]
