<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ModelRelationshipTypeSeeder
 */
class ModelRelationshipTypeSeeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        //this would be locked down to the 20th record.
        //21st and above would be used for something else

        //create record only if required
        $record = LookupType::find(2);
        if ( !$record )
            LookupType::create([
                'id'          => 2,
                'name'        => 'Model relationship types',
                'description' => 'Different types of model columns supported by the platform code',
            ]);

        $columns = [
            [
                'id'             => 21,
                'lookup_type_id' => 2,
                'name'           => 'Single',
                'value'          => 'Single',
                'description'    => 'One to one relationship - belongsTo or has one',
            ],
            [
                'id'             => 22,
                'lookup_type_id' => 2,
                'name'           => 'Multiple',
                'value'          => 'Multiple',
                'description'    => 'One to many relationship - has many',
            ],
            [
                'id'             => 23,
                'lookup_type_id' => 2,
                'name'           => 'Scope',
                'value'          => 'Scope',
                'description'    => 'Date column support y-m-d',
            ],
        ];


        //first check the column definition if present in our lookup and
        //then update / create the record accordingly
        foreach ( $columns as $column ) {
            $record = LookupValue::find($column['id']);

            if ( !$record )
                LookupValue::create($column);
            else {
                foreach ( $column as $key => $value )
                    $record->{$key} = $value;

                $record->save();
            }
        }
    }
}
