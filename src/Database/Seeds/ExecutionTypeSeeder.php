<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;
use Drivezy\LaravelUtility\src\Database\Seeds\BaseSeeder;

/**
 * Class ExecutionTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ExecutionTypeSeeder extends BaseSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {

        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 6,
            'name'        => 'Business Rule Execution Type',
            'description' => 'Business Rule Execution type ',
        ]);

        $records = [
            [
                'id'             => 61,
                'lookup_type_id' => 6,
                'name'           => 'before',
                'value'          => 'Before',
                'description'    => 'Before the execution',
            ],
            [
                'id'             => 62,
                'lookup_type_id' => 6,
                'name'           => 'after',
                'value'          => 'After',
                'description'    => 'After the execution',
            ],
        ];

        foreach ( $records as $record )
            LookupValue::create($record);
    }

    /**
     * Drop the records that were created as part of the migration
     */
    public function drop () {
        //delete the records of the lookup value
        $records = LookupValue::where('lookup_type_id', 6)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(6);
        $record->forceDelete();
    }
}
