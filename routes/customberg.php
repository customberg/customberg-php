<?php

use Customberg\PHP\Customberg;

$config = Customberg::getConfig();

Route::post($config['routes']['preview'], '\Customberg\PHP\Controllers\CustombergController@preview')
    ->middleware($config['routes']['middlewares'])
    ->name('customberg.preview');

// Route::group(['prefix' => 'laraberg-filemanager'], function () {
//     \UniSharp\LaravelFilemanager\Lfm::routes();
// });
