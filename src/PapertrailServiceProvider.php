<?php

namespace Papertrail;

use Illuminate\Support\ServiceProvider;

class PapertrailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {

    }
}
