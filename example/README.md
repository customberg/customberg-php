## Docker usage on local environment

```bash

# create and start
docker-compose up -d
# stop
docker-compose stop
# start
docker-compose start

# install dependencies
docker-compose exec php su app -c 'cd example; composer install'

# generate app key
docker-compose exec php su app -c 'cd example; cp .env.example .env; php artisan key:generate'

# make storage link
docker-compose exec nginx su app -c 'cd /app/example/public/; ln -s ../storage/app/public storage'

# exec inside the php container
docker-compose exec php su app

# rebuild everything
docker-compose up -d --force-recreate --build

```
