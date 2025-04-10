#!/bin/bash

echo "Starting installation process..."

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env file"
fi

# Start Docker containers
echo "Starting Docker containers..."
docker-compose up -d

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Install dependencies
echo "Installing dependencies..."
docker-compose exec -T app composer install

# Generate application key
echo "Generating application key..."
docker-compose exec -T app php artisan key:generate

# Run migrations
echo "Running migrations..."
docker-compose exec -T app php artisan migrate

echo "Installation completed successfully!"
