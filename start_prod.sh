#!/bin/bash

if [ -z "$(which aws)" ]; then
    echo "Error: AWS CLI not found. Please install and configure AWS CLI."
    exit 1
fi

APP_SECRET_NAME="game7-app-secret"
DATABASE_URL_NAME="game7-dburl"

APP_SECRET=$(aws secretsmanager get-secret-value --secret-id "$APP_SECRET_NAME" --query 'SecretString' --output text)
DATABASE_URL=$(aws secretsmanager get-secret-value --secret-id "$DATABASE_URL_NAME" --query 'SecretString' --output text)

if [ -z "$APP_SECRET" ] || [ -z "$DATABASE_URL" ]; then
    echo "Error: Failed to fetch APP_SECRET and/or DATABASE_URL from AWS Secrets Manager."
    exit 1
fi

# Write the environment variables to a temporary file
echo "APP_SECRET=$APP_SECRET" > temp_env
echo "DATABASE_URL=$DATABASE_URL" >> temp_env
echo "CORS_ALLOW_ORIGIN='*'" >> temp_env

if [ -d 'game7-api' ]; then
    cd game7-api && git pull
else
    git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git
    cd game7-api || return
fi

# Pass the temporary environment file to the Docker Compose command
sudo docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file ../temp_env up php caddy -d --build

exit_status=$?
echo "Docker compose exit status: $exit_status"

# Cleanup the temporary environment file
rm temp_env

exit $exit_status
