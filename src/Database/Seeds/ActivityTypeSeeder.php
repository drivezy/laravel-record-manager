<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ActivityTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ActivityTypeSeeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {

        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 1,
            'name'        => 'Client script activity type',
            'description' => 'Different types of activities supported by the client script',
        ]);

        $records = [
            [
                'id'             => 1,
                'lookup_type_id' => 1,
                'name'           => 'onLoad',
                'value'          => 'onLoad',
                'description'    => 'when the page / form is getting loaded',
            ],
            [
                'id'             => 2,
                'lookup_type_id' => 1,
                'name'           => 'onChange',
                'value'          => 'onChange',
                'description'    => 'when the element in the form changes',
            ],
            [
                'id'             => 3,
                'lookup_type_id' => 1,
                'name'           => 'onSubmit',
                'value'          => 'onSubmit',
                'description'    => 'when the form submit button is pressed',
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
        $records = LookupValue::where('lookup_type_id', 1)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(1);
        $record->forceDelete();
    }
}