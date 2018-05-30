<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\ColumnDefinition;

class ModelColumnTypeSeeder {
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
                'id'          => 1,
                'name'        => 'String',
                'description' => 'Alphanumeric column support',
            ],
            [
                'id'          => 2,
                'name'        => 'Number',
                'description' => 'Numeric column support',
            ],
            [
                'id'          => 3,
                'name'        => 'Date',
                'description' => 'Date column support y-m-d',
            ],
            [
                'id'          => 4,
                'name'        => 'Datetime',
                'description' => 'Datetime Column Support y-m-d h:i:s',
            ],
            [
                'id'          => 5,
                'name'        => 'Boolean',
                'description' => 'Yes or no column',
            ],
            [
                'id'          => 6,
                'name'        => 'Reference',
                'description' => 'Referenced column type',
            ],
            [
                'id'          => 7,
                'name'        => 'Select',
                'description' => 'Select Field',
            ],
            [
                'id'          => 8,
                'name'        => 'List',
                'description' => 'Comma separated column type',
            ],
            [
                'id'          => 9,
                'name'        => 'Serializable',
                'description' => 'The column that gets serialized in inside',
            ],
        ];
        foreach ( $columns as $column )
            ColumnDefinition::firstOrCreate($column);
    }
}
