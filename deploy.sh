#!/bin/sh
set -e

# Enter maintenance mode
(php artisan down) || true

# Migrate
php artisan migrate --no-interaction --force

# Clear cache
php artisan optimize

# Restart services
php artisan pulse:restart

# Reload PHP to update opcache
echo "" | sudo /bin/systemctl reload php8.3-fpm.service

# Reload services
sudo supervisorctl reload

# Exit maintenance mode
php artisan up

echo "Application deployed!"