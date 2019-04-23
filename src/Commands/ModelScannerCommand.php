<?php

namespace Drivezy\LaravelRecordManager\Commands;

use Drivezy\LaravelAccessManager\RouteManager;
use Drivezy\LaravelRecordManager\Library\ModelScanner;
use Illuminate\Console\Command;

class ModelScannerCommand extends Command {

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
    public function __construct () {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle () {
        //get the path where drivezy packages are installed
        $drivezyPackagePath = dirname(__DIR__, 3);

        //get all models defined under the required
        ModelScanner::loadModels($drivezyPackagePath . '/laravel-utility/src/Models', 'Drivezy\LaravelUtility\Models');
        ModelScanner::loadModels($drivezyPackagePath . '/laravel-access-manager/src/Models', 'Drivezy\LaravelAccessManager\Models');
        ModelScanner::loadModels($drivezyPackagePath . '/laravel-record-manager/src/Models', 'Drivezy\LaravelRecordManager\Models');
        ModelScanner::loadModels($drivezyPackagePath . '/laravel-admin/src/Models', 'Drivezy\LaravelAdmin\Models');

        //scan and reload all the models defined in the system
        ModelScanner::scanModels();

        //log all routes defined in the system
        RouteManager::logAllRoutes();

    }
}
