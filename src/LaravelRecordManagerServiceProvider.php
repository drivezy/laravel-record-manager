<?php

namespace Drivezy\LaravelRecordManager;

use Drivezy\LaravelRecordManager\Commands\CodeGeneratorCommand;
use Drivezy\LaravelRecordManager\Commands\ModelScannerCommand;
use Drivezy\LaravelRecordManager\Commands\ObserverEventCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelRecordManagerServiceProvider
 * @package Drivezy\LaravelRecordManager
 */
class LaravelRecordManagerServiceProvider extends ServiceProvider {

    /**
     * @var array
     */
    protected $listen = [
        'Illuminate\Mail\Events\MessageSent' => [
            'Drivezy\LaravelRecordManager\Library\Listeners\MailMessageListener',
        ],
    ];

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
    public function register () {

    }
}
