#!/bin/bash

if [ -z "$(which aws)" ]; then
    echo "Error: AWS CLI not found. Please install and configure AWS CLI."
    exit 1
fi

APP_SECRET_NAME="game7-app-secret"
DATABASE_URL_NAME="game7-dburl"
JWT_PASSPHRASE_NAME="game7-jwt-passphrase"

APP_SECRET=$(aws secretsmanager get-secret-value --secret-id "$APP_SECRET_NAME" --query 'SecretString' --output text)
DATABASE_URL=$(aws secretsmanager get-secret-value --secret-id "$DATABASE_URL_NAME" --query 'SecretString' --output text)
JWT_PASSPHRASE=$(aws secretsmanager get-secret-value --secret-id "$JWT_PASSPHRASE_NAME" --query 'SecretString' --output text)


if [ -z "$APP_SECRET" ] || [ -z "$DATABASE_URL" ] || [ -z "$JWT_PASSPHRASE" ]; then
    echo "Error: Failed to fetch APP_SECRET and/or DATABASE_URL from AWS Secrets Manager."
    exit 1
fi

if [ -d 'game7-api' ]; then
    cd game7-api && git pull
else
    git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git
    cd game7-api || return
fi

sudo \
APP_ENV='prod' \
APP_SECRET="$APP_SECRET" \
DATABASE_URL="$DATABASE_URL" \
CORS_ALLOW_ORIGIN='*' \
JWT_PRIVATE_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
JWT_PUBLIC_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
JWT_PASSPHRASE="$JWT_PASSPHRASE" \
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

sudo docker compose exec php sh -c '
	set -e
	apk add openssl
	php bin/console lexik:jwt:generate-keypair --overwrite
	setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
	setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'

exit_status=$?
echo "Docker compose exit status: $exit_status"

exit $exit_status
