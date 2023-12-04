# GAME 7 API

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Guide to use php console

1. First you need to be sure the php container is running
2. Go inside the container : `docker exec -it <container_name> /bin/sh`
3. Inside the container you'll be able to use the commands `php bin/console <command>`

## Init JWT signature keys

1. Start your containers
2. Run : 
```
docker compose exec php sh -c '
    set -e
    apk add openssl
    php bin/console lexik:jwt:generate-keypair
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```

## Init MongoDB Indexes
```
docker compose exec php sh -c 'php bin/console doctrine:mongodb:schema:create --index'
```

## Code quality

- use the following command to check code quality (with level between 0 and 10):

`docker run --init -it --rm -v "$(pwd):/project" -v "$(pwd)/tmp-phpqa:/tmp" -w /project jakzal/phpqa phpstan analyse src --level <level>`

- use the following command to fix php code standards:

`docker run --init -it --rm -v "$(pwd):/project" -v "$(pwd)/tmp-phpqa:/tmp" -w /project jakzal/phpqa php-cs-fixer fix src`

## Deploiement

```shell
docker build . -t shanaziz/game7-caddy:latest --target app_caddy
```

```shell
docker build . -t shanaziz/game7-php:latest --target app_php
```
docker run --env SERVER_NAME="localhost" --env MERCURE_PUBLISHER_JWT_KEY="blabla" --env MERCURE_SUBSCRIBER_JWT_KEY="blabla" -p "127.0.0.1:443:443/TCP" shanaziz/game7-api
