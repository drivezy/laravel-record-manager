<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

class ModelColumnTypeSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        //this would be locked down to the 20th record.
        //21st and above would be used for something else

        LookupType::create([
            'id'          => 1,
            'name'        => 'Data Model Column Types',
            'description' => 'Different types of model columns supported by the platform code',
        ]);
        $columns = [
            [
                'id'             => 1,
                'lookup_type_id' => 1,
                'name'           => 'String',
                'value'          => 'String',
                'description'    => 'Alphanumeric column support',
            ],
            [
                'id'             => 2,
                'lookup_type_id' => 1,
                'name'           => 'Number',
                'value'          => 'Number',
                'description'    => 'Numeric column support',
            ],
            [
                'id'             => 3,
                'lookup_type_id' => 1,
                'name'           => 'Date',
                'value'          => 'Date',
                'description'    => 'Date column support y-m-d',
            ],
            [
                'id'             => 4,
                'lookup_type_id' => 1,
                'name'           => 'Datetime',
                'value'          => 'Datetime',
                'description'    => 'Datetime Column Support y-m-d h:i:s',
            ],
            [
                'id'             => 5,
                'lookup_type_id' => 1,
                'name'           => 'Boolean',
                'value'          => 'Boolean',
                'description'    => 'Yes or no column',
            ],
            [
                'id'             => 6,
                'lookup_type_id' => 1,
                'name'           => 'Reference',
                'value'          => 'Reference',
                'description'    => 'Referenced column type',
            ],
            [
                'id'             => 7,
                'lookup_type_id' => 1,
                'name'           => 'List',
                'value'          => 'List',
                'description'    => 'Comma separated column type',
            ],
            [
                'id'             => 8,
                'lookup_type_id' => 1,
                'name'           => 'Serializable',
                'value'          => 'Serializable',
                'description'    => 'The column that gets serialized in inside',
            ],
        ];
        foreach ( $columns as $column )
            LookupValue::create($column);
    }
}
