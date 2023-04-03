<?php

return [
    'routes' => [
        'prefix' => config('backpack.base.route_prefix', 'admin') . '/customberg',
        'middlewares' => ['web', config('backpack.base.middleware_key', 'admin')],
        'preview' => '/preview',
        'file_upload' => '/file-upload',
    ],
    'default_language' => null, // use null to use laravel locale instead
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
        // prettier-ignore
        'default_allowed_types' => [
            // images
            'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp',
            // video
            'mp4', 'webm', 'mkv', 'flv', 'avi', 'mov', 'm4p', 'mpeg', 'm4v',
            // audio
            'mp3', 'flac', 'm4a', 'wav', 'wma', 'aac',
            // documents
            'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'xml', 'csv',
            // archives
            // 'zip', 'tar', 'bz', 'bz2', 'rar',
        ],
    ],
];
