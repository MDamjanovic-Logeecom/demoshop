#!/bin/bash
set -e

echo "Starting Demoshop setup..."

# Wait for MySQL to be ready
echo "Waiting for database connection..."
until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_NAME}', '${DB_USER}', '${DB_PASS}');" 2>/dev/null; do
  sleep 3
  echo "   → Waiting for MySQL..."
done

echo "Database is up!"

# Install dependencies
if [ ! -d "vendor" ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

# Run migrations and seeders
echo "Running migrations and seeders..."
php Migration/migration.php
php Migration/seedData.php

echo "Setup complete. Starting Apache..."
exec apache2-foreground
