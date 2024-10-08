#!/bin/bash

# Run migrations
php artisan migrate --force

# Run database seeds
php artisan db:seed --force


# Start the Laravel development server
php artisan serve --host=0.0.0.0 --port=8000