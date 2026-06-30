#!/bin/bash
set -e

# Silence Git ownership warnings inside the container
git config --global --add safe.directory /app

# Check if the app code actually exists by looking for composer.json
if [ ! -f "composer.json" ]; then
    echo "--> App code missing. Cloning Picstome repository..."
    git clone https://github.com/picstome/picstome.git /tmp/picstome_repo

    echo "--> Patching composer.json (Removing Flux Pro & fixing network bindings)..."
    php -r "
        \$file = '/tmp/picstome_repo/composer.json';
        \$data = json_decode(file_get_contents(\$file), true);
        
        // 1. Strip commercial Flux Pro requirements
        if (isset(\$data['require']['livewire/flux-pro'])) {
            unset(\$data['require']['livewire/flux-pro']);
        }
        if (isset(\$data['repositories']['flux-pro'])) {
            unset(\$data['repositories']['flux-pro']);
        }
        if (empty(\$data['repositories'])) {
            unset(\$data['repositories']);
        }
        
        // 2. Fix Docker networking bounds inside the dev script array
        if (isset(\$data['scripts']['dev'])) {
            foreach (\$data['scripts']['dev'] as &\$script) {
                // Change 'php artisan serve' to 'php artisan serve --host=0.0.0.0'
                \$script = str_replace('php artisan serve', 'php artisan serve --host=0.0.0.0', \$script);
                // Change 'npm run dev' to 'npm run dev -- --host' so Vite exposes itself to the host network
                \$script = str_replace('npm run dev', 'npm run dev -- --host', \$script);
            }
        }
        
        file_put_contents(\$file, json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    "

    # Copy the fully modified codebase into the working app directory
    cp -a /tmp/picstome_repo/. /app/
    rm -rf /tmp/picstome_repo
else
    echo "--> App code found. Ensuring network bindings are patched..."
    # Fallback patch if files already exist on the host system from the last run
    php -r "
        \$file = 'composer.json';
        \$data = json_decode(file_get_contents(\$file), true);
        if (isset(\$data['scripts']['dev'])) {
            foreach (\$data['scripts']['dev'] as &\$script) {
                if (strpos(\$script, '--host=0.0.0.0') === false) {
                    \$script = str_replace('php artisan serve', 'php artisan serve --host=0.0.0.0', \$script);
                }
                if (strpos(\$script, '-- --host') === false) {
                    \$script = str_replace('npm run dev', 'npm run dev -- --host', \$script);
                }
            }
            file_put_contents(\$file, json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    "
fi

# Install Composer dependencies
if [ ! -d "vendor" ]; then
    echo "--> Installing PHP dependencies (Free Flux OSS tier)..."
    composer install --no-interaction
fi

# Install NPM dependencies
if [ ! -d "node_modules" ]; then
    echo "--> Installing Node dependencies..."
    npm install
fi

# Set up Laravel environment, SQLite database, and assets
if [ ! -f ".env" ]; then
    echo "--> Setting up .env and database..."
    cp .env.example .env
    php artisan key:generate --force
    
    mkdir -p database
    touch database/database.sqlite
    
    echo "--> Running migrations..."
    php artisan migrate:fresh --force
    
    echo "--> Creating storage symlink..."
    php artisan storage:link
fi

echo "--> Compiling assets and starting development servers..."
exec composer run dev