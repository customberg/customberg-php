Demo GIF

<img src="customberg-demo.gif?raw=true">

# Instalation

```bash
composer require customberg/customberg-php:"*"
```

Add vendor files to your project (CSS, JS, Config, Views):

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider"
php artisan vendor:publish --provider="Customberg\PHP\CustombergServiceProvider"
```

# Usage

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

# Update

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

# Roadmap:

|          |                               |
| -------- | ----------------------------- |
| &#x2610; | A checkbox and a switch field |
| &#x2610; | Radio field                   |
| &#x2610; | Slider field                  |
| &#x2610; | Google Maps field             |
| &#x2610; | Documentation !               |
| &#x2610; | Icon picker field             |

# Changelog:

### v0.14 - 2024-08-07

-   Bugfix: Preview API while rendering a block with multilanguage, when i'm selecting the new language that does not have translation saved.

### v0.13 - 2024-07-08

-   Allow laravel 11 installation

### v0.12 - 2023-11-09

-   Bugfix error cannot redeclare str_replace_limit

### v0.11 - 2023-11-08

-   Update requirements to laravel 10

### v0.10 - 2023-04-02

-   Added file extension validation to `upload_image`.
    <br />Use `'allowed_types' => ['png', 'jpg', 'webp', 'svg'],`
-   Added `upload_file` for files without preview.
    <br />Use `'multiple' => true,` if you want multiple files in the same field.

### v0.9 - 2023-01-01

-   Multilanguage fine tuning, made configurable language list and bugfixing
-   Multilanguage only supports fields `text` and `rich_text`, and works inside repeatable field.

### v0.8 - 2022-12-31

-   Fine tuning for repeatable field frontend
-   Added buttons to move and duplicate a item inside repeatable
-   Added example block for repeatable

### v0.7 - 2022-12-30

-   First version published to packagist.org ! 🎉🥳
-   Only available fields are: `text, rich_text, color, upload_image`
-   Repeatable is present but untested. Multilanguage present but untested and hardcoded.
