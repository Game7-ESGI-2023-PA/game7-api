#!/bin/bash

if [ $# -ne 3 ]; then
    echo "Error: Please provide APP_SECRET, DATABASE_URL and GIT_SSH_PASSPHRASE a as parameters."
    exit 1
fi

eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519 -p "$3"

if [ -d "game7-api" ]; then
    cd game7-api || return
    git pull
else
    git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git game7-api
fi

export APP_SECRET=$1
export DATABASE_URL=$2
export CORS_ALLOW_ORIGIN='*'

docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
