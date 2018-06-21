<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

class FormMethodSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {

        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 3,
            'name'        => 'Custom Form Method Types',
            'description' => 'Different types of methods supported by custom form',
        ]);

        $records = [
            [
                'id'             => 21,
                'lookup_type_id' => 3,
                'name'           => 'GET',
                'value'          => 'GET',
                'description'    => 'get call on the remote api server',
            ],
            [
                'id'             => 22,
                'lookup_type_id' => 3,
                'name'           => 'POST',
                'value'          => 'POST',
                'description'    => 'post call on the remote api server',
            ],
            [
                'id'             => 23,
                'lookup_type_id' => 3,
                'name'           => 'PUT',
                'value'          => 'PUT',
                'description'    => 'put call on the remote api server',
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
        $records = LookupValue::where('lookup_type_id', 3)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(3);
        $record->forceDelete();
    }
}