#!/usr/bin/env bash
# echo "Seteando TZ"
# timedatectl set-timezone America/Argentina/Cordoba

echo "Running composer"
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

# echo "Running migrations..."
# php artisan migrate --force

echo "Generando storage"
php artisan storage:link

echo "Lista de rutas"
php artisan route:list

echo "Iniciando server"
php artisan serve --host=0.0.0.0 --port=443
