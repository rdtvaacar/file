<?php

namespace Acr\File;

use Acr\File\Controllers\AcrFileController;
use Illuminate\Support\ServiceProvider;

class AcrFileServiceProviders extends ServiceProvider
{
    public function boot()
    {
        include(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'acr_file_v');
    }

    public function register()
    {
        $this->app->bind('AcrFile', function(){
            return new AcrFileController();
        });
    }
}