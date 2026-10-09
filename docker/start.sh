#!/bin/bash
set -e

echo "Iniciando Banco Central..."

# Limpiar cachés previas
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Cachear para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear storage link
php artisan storage:link || true

# Arrancar PHP-FPM en background
php-fpm -D

# Arrancar nginx en foreground
nginx -g "daemon off;"
