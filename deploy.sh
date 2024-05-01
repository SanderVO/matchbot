#!/bin/sh
set -e

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true

# Migrate
php artisan migrate

# Clear cache
php artisan optimize

# Reload services
supervisorctl reload

# Exit maintenance mode
php artisan up

echo "Application deployed!"