<?php

return [
    'routes' => [
        'preview' => config('backpack.base.route_prefix', 'admin') . '/customberg/preview',
        'middlewares' => ['web', config('backpack.base.middleware_key', 'admin')],
    ],
    'languages' => [
        'en' => 'English',
    ],
    'assets' => [
        'stack_scripts' => 'crud_fields_scripts',
        'stack_styles' => 'crud_fields_styles',
    ],
];
