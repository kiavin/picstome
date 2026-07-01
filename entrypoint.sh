#!/bin/bash
set -e

# 1. Initialize data folders on the persistent storage volume
mkdir -p /data/minio_storage /data/mailpit_storage /data/database

# 2. Re-route SQLite to persistent data storage
if [ ! -f /data/database/database.sqlite ]; then
    echo "--> Initializing clean SQLite database file..."
    touch /data/database/database.sqlite
fi

# Generate the exact .env configuration provided, adapted for the local appliance network
cat << 'EOF' > .env
APP_NAME=Picstome
APP_ENV=local
APP_KEY=base64:FO8FTT2bOFkxkmhRd3JDdOARlUuyanGlpUUd0u2cuIE=
APP_DEBUG=true
APP_TIMEZONE=UTC
# Aligned to your exposed host port
APP_URL=http://localhost:8091
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
DB_DATABASE=/data/database/database.sqlite
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Aligned for local SMTP relay via Docker
MAIL_MAILER=smtp
MAIL_SCHEME=null
# Appliance override: Mailpit runs internally on 127.0.0.1
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@picstome.local"
MAIL_FROM_NAME="${APP_NAME}"

# MinIO S3 Credentials (Appliance defaults)
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=picstome-galleries

# Appliance override: MinIO runs internally on 127.0.0.1
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_URL=http://localhost:9000/picstome-galleries
VITE_APP_NAME="${APP_NAME}"

STRIPE_KEY=your-stripe-key
STRIPE_LIVE_SECRET=your-stripe-live-secret
STRIPE_TEST_SECRET=your-stripe-test-secret
STRIPE_SECRET=your-stripe-secret
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret
STRIPE_EN_PRICING_TABLE_ID=your-stripe-en-pricing-table-id
STRIPE_ES_PRICING_TABLE_ID=your-stripe-es-pricing-table-id

STRIPE_100GB_PRICES=price_xxx_100gb_1,price_xxx_100gb_2
STRIPE_250GB_PRICES=price_xxx_250gb_1,price_xxx_250gb_2
STRIPE_1000GB_PRICES=price_xxx_1000gb_1,price_xxx_1000gb_2

PICSTOME_ADMIN_EMAILS=admin@example.com
PICSTOME_PHOTO_CDN_DOMAIN=

LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=s3
SHORT_URL_DOMAIN=

HONEYBADGER_API_KEY=
HONEYBADGER_VERIFY_SSL=

ACUMBAMAIL_AUTH_TOKEN=your-acumbamail-auth-token
ACUMBAMAIL_LIST_ID=your-acumbamail-list-id
ACUMBAMAIL_LIST_ID_ES=your-acumbamail-spanish-list-id
EOF

# 3. Handle default asset encryption generation
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "--> Generating Application Encryption Key..."
    php artisan key:generate --no-interaction
fi

echo "--> Testing storage symlinks..."
php artisan storage:link --no-interaction || true

echo "--> Refreshing database schemas..."
php artisan migrate --force

# 4. Background service loop to establish the MinIO bucket configuration automatically
(
    echo "--> Checking for active internal S3 engine..."
    until curl -s http://127.0.0.1:9000 > /dev/null; do sleep 1; done
    echo "--> S3 Engine online. Executing policy overrides..."
    minio-client alias set localminio http://127.0.0.1:9000 minioadmin minioadmin
    minio-client mb localminio/picstome-galleries --ignore-existing
    minio-client anonymous set download localminio/picstome-galleries
    echo "--> Internal S3 configuration complete!"
) &

# 5. Hand control off to Supervisor
echo "--> Launching system manager infrastructure..."
exec /usr/bin/supervisord -c /app/supervisord.conf
