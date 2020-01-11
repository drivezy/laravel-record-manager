<?php

namespace Drivezy\LaravelRecordManager;

use Drivezy\LaravelRecordManager\Commands\CodeGeneratorCommand;
use Drivezy\LaravelRecordManager\Commands\ModelScannerCommand;
use Drivezy\LaravelRecordManager\Commands\ObserverEventCommand;
use Drivezy\LaravelRecordManager\Library\Listeners\EventQueueListener;
use Drivezy\LaravelUtility\Events\EventQueueRaised;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelRecordManagerServiceProvider
 * @package Drivezy\LaravelRecordManager
 */
class LaravelRecordManagerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot ()
    {
        Event::listen(EventQueueRaised::class, EventQueueListener::class);

        //load routes defined out here
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        //load migrations as part of this package
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        //load command defined in the system
        if ( $this->app->runningInConsole() ) {
            $this->commands([
                CodeGeneratorCommand::class,
                ModelScannerCommand::class,
                ObserverEventCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register ()
    {

    }
}
