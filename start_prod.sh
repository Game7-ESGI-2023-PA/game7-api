#!/bin/bash

if [ -d 'game7-api' ]; then
	cd game7-api && git pull
else
	git clone git@github.com:Game7-ESGI-2023-PA/game7-api.git
	cd game7-api || return
fi

sudo docker compose -f docker-compose.yml -f docker-compose.prod.yml up php caddy -d --build

exit_status=$?
echo "Docker compose exit status: $exit_status"

exit $exit_status
