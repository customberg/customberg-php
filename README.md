## Instalation

```bash
composer require customberg/customberg-php:"*"
```

Add vendor files to your project (CSS, JS, Config, Views):

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider"
php artisan vendor:publish --provider="Customberg\PHP\CustombergServiceProvider"
```

The model that you want to use this block editor should have:

```php
use VanOns\Laraberg\Traits\RendersContent;

class Page extends Model
{
    use RendersContent;
}
```

In the backpack crud controller you must create the crud field like this:

```php
CRUD::field('content')
    ->label('Page content')
    ->type('customberg')
    ->view_namespace('customberg::');
```

## Update

After an update, get the latest assets using:

```bash
php artisan vendor:publish --tag="customberg-assets" --force
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider" --tag="public" --force
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
