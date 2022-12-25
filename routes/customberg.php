<?php

use Customberg\PHP\Customberg;

$config = Customberg::getConfig();

Route::post(
    $config['routes']['prefix'] . $config['routes']['preview'],
    '\Customberg\PHP\Controllers\CustombergController@preview'
)
    ->middleware($config['routes']['middlewares'])
    ->name('customberg.preview');

Route::post(
    $config['routes']['prefix'] . $config['routes']['file_upload'],
    '\Customberg\PHP\Controllers\CustombergController@file_upload'
)
    ->middleware($config['routes']['middlewares'])
    ->name('customberg.file_upload');
