#!/bin/bash

if [ "$1" == "up" ]; then
    docker-compose up -d
elif [ "$1" == "down" ]; then
    docker-compose down
elif [ "$1" == "build" ]; then
    docker-compose build
elif [ "$1" == "bash" ]; then
    docker-compose exec app bash
else
    echo "Available commands:"
    echo "  up    - Start containers"
    echo "  down  - Stop containers"
    echo "  build - Rebuild containers"
    echo "  bash  - Access app container shell"
fi
