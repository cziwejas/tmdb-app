#!/bin/bash
set -e

# Czekaj na dostępność bazy danych
echo "Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is up - executing command"

# Instalacja zależności jeśli nie istnieją
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "Installing NPM dependencies..."
    npm install
fi

# Generowanie klucza aplikacji jeśli nie istnieje
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Uruchomienie migracji
echo "Running migrations..."
php artisan migrate --force

# Optymalizacja
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Budowanie assetów (opcjonalnie, może być pominięte w produkcji)
if [ -f "package.json" ]; then
    echo "Building assets..."
    npm run build || echo "Asset build failed, continuing..."
fi

echo "Application is ready!"

# Wykonaj przekazaną komendę
exec "$@"
