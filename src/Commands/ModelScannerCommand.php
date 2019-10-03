<?php

namespace Drivezy\LaravelRecordManager\Commands;

use Drivezy\LaravelAccessManager\RouteManager;
use Drivezy\LaravelRecordManager\Library\ModelScanner;
use Illuminate\Console\Command;

class ModelScannerCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all the models and all the routes present in the system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle ()
    {
        //get the path where drivezy packages are installed
        $drivezyPackagePath = dirname(__DIR__, 3);

        //find all directories that are part of the drivezy packages
        $packages = array_diff(scandir($drivezyPackagePath), array('..', '.'));
        foreach ( $packages as $package ) {
            //get the namespace of the given package
            $namespace = implode('', array_map('ucfirst', explode('-', $package)));

            echo 'processing Drivezy\\' . $namespace . '\\Models' . PHP_EOL;
            ModelScanner::loadModels($drivezyPackagePath . '/' . $package . '/src/Models', 'Drivezy\\' . $namespace . '\\Models');

        }

        //scan and reload all the models defined in the system
        ModelScanner::scanModels();

        //log all routes defined in the system
        RouteManager::logAllRoutes();

    }
}
