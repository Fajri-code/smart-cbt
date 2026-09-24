#!/bin/sh
set -e

echo "[Smart-CBT] Starting container initialization..."

# 1. Sync public template assets to public volume if mounted
if [ -d "/var/www/html/public_template/build" ]; then
    echo "[Smart-CBT] Syncing Vite build assets to public volume..."
    mkdir -p /var/www/html/public/build
    cp -ru /var/www/html/public_template/build/. /var/www/html/public/build/ 2>/dev/null || true
fi

# 2. Clear stale cache files to prevent class/config conflicts
echo "[Smart-CBT] Clearing stale cache files..."
rm -f /var/www/html/bootstrap/cache/*.php

# 3. Check database connection and auto-create database if not exists
echo "[Smart-CBT] Checking database connection..."
php -r '
$driver = getenv("DB_CONNECTION") ?: "pgsql";
$host   = getenv("DB_HOST") ?: "db";
$port   = getenv("DB_PORT") ?: ($driver === "pgsql" ? 5432 : 3306);
$user   = getenv("DB_USERNAME") ?: "smart_cbt";
$pass   = getenv("DB_PASSWORD") ?: "smart_cbt_password";
$db     = getenv("DB_DATABASE") ?: "smart_cbt";

$maxTries = 15;
$connected = false;

for ($i = 1; $i <= $maxTries; $i++) {
    try {
        if ($driver === "pgsql") {
            $pdo = new PDO("pgsql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);
            echo "[Smart-CBT] PostgreSQL \"{$db}\" ready on {$host}:{$port}.\n";
        } else {
            $rootUser = getenv("DB_ROOT_USERNAME") ?: "root";
            $rootPass = getenv("DB_ROOT_PASSWORD") ?: $pass;
            $pdo = new PDO("mysql:host={$host};port={$port}", $rootUser, $rootPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS \`{$db}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            echo "[Smart-CBT] MySQL \"{$db}\" ready on {$host}:{$port}.\n";
        }
        $connected = true;
        break;
    } catch (Exception $e) {
        echo "[Smart-CBT] Waiting for {$driver} ({$host}:{$port})... Attempt {$i}/{$maxTries} ({$e->getMessage()})\n";
        sleep(2);
    }
}

if (!$connected) {
    echo "[Smart-CBT] Warning: Database connection timed out. Proceeding anyway...\n";
}
'

# 4. Run Laravel migrations (skip if running queue or scheduler to avoid race condition)
if [ "$1" = "php-fpm" ]; then
    echo "[Smart-CBT] Running database migrations..."
    php artisan migrate --force || echo "[Smart-CBT] Migration step completed with notices."

    echo "[Smart-CBT] Ensuring storage symlink..."
    php artisan storage:link || true

    if [ "$APP_ENV" = "production" ]; then
        echo "[Smart-CBT] Caching configuration, routes, and views for production..."
        php artisan optimize:clear || true
        php artisan config:cache || true
        php artisan route:cache || true
        php artisan view:cache || true
        php artisan event:cache || true
    fi
fi

# 5. Fix permissions for storage and bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "[Smart-CBT] Container ready. Executing: $@"
exec "$@"
