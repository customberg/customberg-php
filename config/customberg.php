<?php

return [
    'routes' => [
        'prefix' => config('backpack.base.route_prefix', 'admin') . '/customberg',
        'middlewares' => ['web', config('backpack.base.middleware_key', 'admin')],
        'preview' => '/preview',
        'file_upload' => '/file-upload',
    ],
    'default_language' => 'en', // use null to use laravel locale instead
    'languages' => [
        'en' => 'English',
        'ro' => 'Romanian',
    ],
    'assets' => [
        'stack_scripts' => 'crud_fields_scripts',
        'stack_styles' => 'crud_fields_styles',
    ],
    'upload' => [
        'disk' => 'public',
        'path' => 'blocks/{Y}/{m}',
    ],
];
