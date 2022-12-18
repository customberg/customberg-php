## Docker usage on local environment

```bash

# create and start
docker-compose up -d
# stop
docker-compose stop
# start
docker-compose start

# install dependencies
docker-compose exec php su app -c 'composer install'

# run migrations and seeders
docker-compose exec php su app -c 'cd example; php artisan migrate:fresh --seed'

# make storage link
docker-compose exec nginx su app -c 'cd /app/example/public/; ln -s ../storage/app/public storage'

# exec inside the php container
docker-compose exec php su app

# rebuild everything
docker-compose up -d --force-recreate --build

```
