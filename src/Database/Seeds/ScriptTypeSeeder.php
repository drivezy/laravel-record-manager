<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;
use Drivezy\LaravelUtility\src\Database\Seeds\BaseSeeder;

/**
 * Class ScriptTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ScriptTypeSeeder extends BaseSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {

        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 2,
            'name'        => 'Script Types',
            'description' => 'Different types of scripts supported into our system',
        ]);

        $records = [
            [
                'id'             => 11,
                'lookup_type_id' => 2,
                'name'           => 'javascript',
                'value'          => 'JS',
                'description'    => 'Javascript into our system',
            ],
            [
                'id'             => 12,
                'lookup_type_id' => 2,
                'name'           => 'php',
                'value'          => 'PHP',
                'description'    => 'php into our system',
            ],
            [
                'id'             => 13,
                'lookup_type_id' => 2,
                'name'           => 'sql',
                'value'          => 'SQL',
                'description'    => 'sql into our system',
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
        $records = LookupValue::where('lookup_type_id', 2)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(2);
        $record->forceDelete();
    }
}
