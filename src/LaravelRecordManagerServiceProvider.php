<?php

namespace Drivezy\LaravelRecordManager;

use Drivezy\LaravelRecordManager\Commands\CodeGeneratorCommand;
use Illuminate\Support\ServiceProvider;

class LaravelRecordManagerServiceProvider extends ServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot () {
        //load routes defined out here
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        //load migrations as part of this package
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        //load command defined in the system
        if ( $this->app->runningInConsole() ) {
            $this->commands([
                CodeGeneratorCommand::class,
            ]);
        }

        //publish the seeds
        $this->publishes([
            __DIR__ . '/Database/Seeds' => database_path('seeds'),
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
