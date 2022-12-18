
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
docker-compose exec php su app -c 'php artisan migrate:fresh --seed'

# make storage link
docker-compose exec nginx su app -c 'cd /app/public/; ln -s ../storage/app/public storage'

# exec inside the php container
docker-compose exec php su app

# open mysql client
docker-compose exec php su app -c 'mysql -h db -u laravel -psecret laravel'

# rebuild everything
docker-compose up -d --force-recreate --build

```
