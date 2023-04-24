#!/bin/bash

if [ $# -ne 2 ]; then
    echo "Error: Please provide APP_SECRET, DATABASE_URL a as parameters."
    exit 1
fi

export APP_SECRET=$1
export DATABASE_URL=$2
export CORS_ALLOW_ORIGIN='*'

docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
