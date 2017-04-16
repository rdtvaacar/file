<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'Acr\File\Controllers', 'prefix' => 'acr/file'], function () {
        Route::get('/kontrol', 'AcrFileController@kontrol');
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/upload', 'AcrFileController@index');
            Route::post('/upload', 'AcrFileController@index');
            Route::delete('/upload', 'AcrFileController@index');


        });
    });
});