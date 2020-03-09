<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Library\DictionaryManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\Database\Seeds\BaseSeeder;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class DataModelSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class DataModelSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
        $records = [
            [
                'name'                => 'User',
                'description'         => 'User defined in the system',
                'namespace'           => config('custom-utility.app_namespace'),
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
