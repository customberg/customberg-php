<?php

use Customberg\PHP\Customberg;

$config = app(Customberg::class)->getConfig();

Route::post($config['routes']['preview'], '\Customberg\PHP\Controllers\CustombergController@preview')->middleware(
    $config['routes']['middlewares']
);

// Route::group(['prefix' => 'laraberg-filemanager'], function () {
//     \UniSharp\LaravelFilemanager\Lfm::routes();
// });
