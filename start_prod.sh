##!/bin/bash
#
#APP_SECRET="$1"
#MONGODB_URL="$2"
#MONGODB_DB="$3"
#JWT_PASSPHRASE="$4"
#MERCURE_JWT_SECRET="$5"
#
#if [ -z "$APP_SECRET" ] || [ -z "$MONGODB_URL" ] || [ -z "$MONGODB_DB" ] || [ -z "$JWT_PASSPHRASE" ] || [ -z "$MERCURE_JWT_SECRET" ]; then
#    echo "Error: Missing parameters. The script requires APP_SECRET, MONGODB_URL, MONGODB_DB and JWT_PASSPHRASE as named parameters."
#    exit 1
#fi
#
#if [ -d 'game7-api' ]; then
#    cd game7-api && git pull
#else
#    git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git
#    cd game7-api || return
#fi
#
## Delete old containers : makes downtime, but fix caching issues
#sudo docker compose stop
#sudo docker compose rm
#
#sudo \
#APP_ENV='prod' \
#APP_SECRET="$APP_SECRET" \
#MONGODB_URL="$MONGODB_URL" \
#MONGODB_DB="$MONGODB_DB" \
#CORS_ALLOW_ORIGIN='*' \
#JWT_PRIVATE_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
#JWT_PUBLIC_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
#JWT_PASSPHRASE="$JWT_PASSPHRASE" \
#CADDY_MERCURE_JWT_SECRET="$MERCURE_JWT_SECRET" \
#MERCURE_URL="http://caddy/.well-known/mercure" \
#MERCURE_PUBLIC_URL="https://api.game7app.com/.well-known/mercure" \
#docker compose -f docker-compose.yml -f docker-compose.prod.yml up php caddy game-dispatcher -d --build
#
#sudo docker compose exec php sh -c '
#	set -e
#	apk add openssl
#	php bin/console lexik:jwt:generate-keypair --overwrite
#	setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
#	setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
#'
#
#sudo docker compose exec php sh -c 'php bin/console doctrine:mongodb:schema:create --index'
#
#sudo docker compose exec php sh -c 'php bin/console cache:clear'
#
#exit_status=$?
#echo "Docker compose exit status: $exit_status"
#
#exit $exit_status

APP_ENV='dev' \
APP_SECRET="e128c5fd54a8a77203485244f88f05fb" \
MONGODB_URL=mongodb://admin:password@mongodb:27017 \
MONGODB_DB="game7" \
CORS_ALLOW_ORIGIN='*' \
JWT_PRIVATE_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
JWT_PUBLIC_KEY_PATH=%kernel.project_dir%/config/jwt/private.pem \
JWT_PASSPHRASE="d2f1a67836debcee23ae3f5b24b499cac984b95908e8a2f955592d90b9236994" \
CADDY_MERCURE_JWT_SECRET="!ChangeThisMercureHubJWTSecretKey!" \
MERCURE_URL="http://caddy/.well-known/mercure" \
MERCURE_PUBLIC_URL="https://api.game7app.com/.well-known/mercure" \
docker compose up php caddy game-dispatcher -d --build

docker compose exec php sh -c '
	set -e
	apk add openssl
	php bin/console lexik:jwt:generate-keypair --overwrite
	setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
	setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'

docker compose exec php sh -c 'php bin/console doctrine:mongodb:schema:create --index'

docker compose exec php sh -c 'php bin/console cache:clear'

exit_status=$?
echo "Docker compose exit status: $exit_status"

exit $exit_status
