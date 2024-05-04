#!/bin/sh
set -e

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true

# Migrate
php artisan migrate

# Clear cache
php artisan optimize

# Restart services
php artisan pulse:restart

# Reload PHP to update opcache
echo "" | sudo /bin/systemctl reload php8.3-fpm.service

# Reload services
supervisorctl reload

# Exit maintenance mode
php artisan up

echo "Application deployed!"