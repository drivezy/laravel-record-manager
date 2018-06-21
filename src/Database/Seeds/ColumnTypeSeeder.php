<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\ColumnDefinition;

class ColumnTypeSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        //this would be locked down to the 20th record.
        //21st and above would be used for something else

        $columns = [
            [
                'id'                    => 1,
                'name'                  => 'String',
                'description'           => 'Alphanumeric column support',
                'supported_identifiers' => 'string,text',
            ],
            [
                'id'                    => 2,
                'name'                  => 'Number',
                'description'           => 'Numeric column support',
                'supported_identifiers' => 'integer,float,decimal',
            ],
            [
                'id'                    => 3,
                'name'                  => 'Date',
                'description'           => 'Date column support y-m-d',
                'supported_identifiers' => 'date',
            ],
            [
                'id'                    => 4,
                'name'                  => 'Datetime',
                'description'           => 'Datetime Column Support y-m-d h:i:s',
                'supported_identifiers' => 'datetime',
            ],
            [
                'id'                    => 5,
                'name'                  => 'Boolean',
                'description'           => 'Yes or no column',
                'supported_identifiers' => 'boolean',
            ],
            [
                'id'                    => 6,
                'name'                  => 'Reference',
                'description'           => 'Referenced column type',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 7,
                'name'                  => 'Select',
                'description'           => 'Select Field',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 8,
                'name'                  => 'List',
                'description'           => 'Comma separated column type',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 9,
                'name'                  => 'Serializable',
                'description'           => 'The column that gets serialized in inside',
                'supported_identifiers' => '',
            ],
        ];
        foreach ( $columns as $column )
            ColumnDefinition::firstOrCreate($column);
    }
}
