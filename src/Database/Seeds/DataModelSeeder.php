<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Library\DictionaryManager;
use Drivezy\LaravelRecordManager\Library\ModelScanner;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\src\Database\Seeds\BaseSeeder;

/**
 * Class DataModelSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class DataModelSeeder extends BaseSeeder {
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

        $dataModels = DataModel::get();
        foreach ( $dataModels as $model ) {
            ( new DictionaryManager($model) )->process();
        }
    }
}
