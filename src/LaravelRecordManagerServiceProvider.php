<?php

namespace Drivezy\LaravelRecordManager;

use Illuminate\Support\ServiceProvider;

class LaravelRecordManagerServiceProvider extends ServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot () {
        $this->publishes([
            __DIR__ . '/Database/Migrations' => database_path('migrations'),
            __DIR__ . '/Database/Seeds'      => database_path('seeds'),
        ], 'migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register () {
    }
}
