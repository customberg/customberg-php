## Instalation

```bash
composer require customberg/customberg-php:"*"
```

Add vendor files to your project (CSS, JS, Config, Views):

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider"
php artisan vendor:publish --provider="Customberg\PHP\CustombergServiceProvider"
```

# Example project

```bash
git clone https://github.com/customberg/customberg-php
cd customberg-php/example
docker compose up -d
docker-compose exec php su app -c 'cd example; composer install'
docker-compose exec php su app -c 'cd example; cp .env.example .env; php artisan key:generate'
docker-compose exec nginx su app -c 'cd /app/example/public/; ln -s ../storage/app/public storage'
```

Navigate to http://localhost:8084 and press login.

Enjoy !
