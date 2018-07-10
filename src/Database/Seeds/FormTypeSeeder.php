<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

class FormTypeSeeder {

    public function run () {
        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 5,
            'name'        => 'Different types of forms',
            'description' => 'Form type like alert / prompt / regular',
        ]);

        $records = [
            [
                'id'             => 51,
                'lookup_type_id' => 5,
                'name'           => 'Custom',
                'value'          => 'Custom Form',
                'description'    => 'A custom form which is regularly created',
            ],
            [
                'id'             => 52,
                'lookup_type_id' => 5,
                'name'           => 'Alert',
                'value'          => 'Alert',
                'description'    => 'Alert form',
            ],
            [
                'id'             => 53,
                'lookup_type_id' => 5,
                'name'           => 'Prompt',
                'value'          => 'Prompt',
                'description'    => 'Prompt with a callback on the value',
            ],
        ];

        foreach ( $records as $record )
            LookupValue::create($record);
    }

    public function drop () {
        //delete the records of the lookup value
        $records = LookupValue::where('lookup_type_id', 5)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(5);
        $record->forceDelete();
    }
}