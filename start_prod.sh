#!/bin/bash

if [ $# -ne 2 ]; then
    echo "Error: Please provide APP_SECRET, DATABASE_URL a as parameters."
    exit 1
fi

if [ -d 'game7-api' ]; then
	cd game7-api && git pull
else
	git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git
	cd game7-api || return
fi

APP_SECRET=$1 \
DATABASE_URL=$2 \
CORS_ALLOW_ORIGIN='*' \
sudo docker compose -f docker-compose.yml -f docker-compose.prod.yml up php caddy  --build
