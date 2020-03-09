<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\ColumnDefinition;
use Drivezy\LaravelUtility\Database\Seeds\BaseSeeder;

/**
 * Class ColumnTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ColumnTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
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
            [
                'id'                    => 10,
                'name'                  => 'Script',
                'description'           => 'Will create a script',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 11,
                'name'                  => 'Form',
                'description'           => 'CustomForm against that entity',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 12,
                'name'                  => 'Upload',
                'description'           => 'file upload column',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 13,
                'name'                  => 'MultipleUpload',
                'description'           => 'multiple file one',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 14,
                'name'                  => 'Time',
                'description'           => 'Just the time factor of the date time',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 15,
                'name'                  => 'MultipleSelect',
                'description'           => 'Selection of multiple records',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 16,
                'name'                  => 'Text',
                'description'           => 'Large input field',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 17,
                'name'                  => 'Password',
                'description'           => 'field wherein we dont want to show the input params',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 18,
                'name'                  => 'Encrypted',
                'description'           => 'field wherein data is encrypted',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 19,
                'name'                  => 'Month',
                'description'           => 'Just the Ym param of datetime',
                'supported_identifiers' => '',
            ],
            [
                'id'                    => 20,
                'name'                  => 'SourceColumn',
                'description'           => 'Generic source column which connected to data model one',
                'supported_identifiers' => '',
            ],

        ];
        foreach ( $columns as $column )
            ColumnDefinition::firstOrCreate($column);
    }
}
