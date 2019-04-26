<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Database\Seeds\BaseSeeder;
use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ModelEventTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ModelEventTypeSeeder extends BaseSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 7,
            'name'        => 'Model Event Type',
            'description' => 'Insert | Update | Delete',
        ]);

        $records = [
            [
                'id'             => 71,
                'lookup_type_id' => 7,
                'name'           => 'New Record',
                'value'          => 'Insertion',
                'description'    => 'New record | Insertion',
            ],
            [
                'id'             => 72,
                'lookup_type_id' => 7,
                'name'           => 'Updated Record',
                'value'          => 'Update',
                'description'    => 'After the update of any record',
            ],
            [
                'id'             => 73,
                'lookup_type_id' => 7,
                'name'           => 'Deleted Record',
                'value'          => 'Deletion',
                'description'    => 'Deleted Record',
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
        $records = LookupValue::where('lookup_type_id', 7)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(7);
        $record->forceDelete();
    }
}
