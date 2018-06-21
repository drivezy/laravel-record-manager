<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Library\DictionaryManager;
use Drivezy\LaravelRecordManager\Library\ModelScanner;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class DataModelSeeder
 */
class DataModelSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        $records = [
            [
                'name'                => 'User',
                'description'         => 'User defined in the system',
                'namespace'           => config('utility.app_namespace'),
                'allowed_permissions' => 'raed',
                'table_name'          => LaravelUtility::getUserTable(),
            ],
        ];

        foreach ( $records as $record )
            DataModel::create($record);

        ModelScanner::loadModels(base_path('vendor/drivezy/laravel-utility/src/Models'), 'Drivezy\LaravelUtility\Models');
        ModelScanner::loadModels(base_path('vendor/drivezy/laravel-access-manager/src/Models'), 'Drivezy\LaravelAccessManager\Models');

        $dataModels = DataModel::get();
        foreach ( $dataModels as $model ) {
            ( new DictionaryManager($model) )->process();
        }
    }
}