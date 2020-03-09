<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\RelationshipDefinition;
use Drivezy\LaravelUtility\Database\Seeds\BaseSeeder;
use Drivezy\LaravelUtility\Models\LookupType;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ModelRelationshipTypeSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class ModelRelationshipTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {

        //create client script against the record
        LookupType::firstOrCreate([
            'id'          => 4,
            'name'        => 'Model Relationship Types',
            'description' => 'Different types of relationships maintained within the models',
        ]);

        $records = [
            [
                'id'             => 41,
                'lookup_type_id' => 4,
                'name'           => 'Single',
                'value'          => 'Single',
                'description'    => 'One to one relationship',
            ],
            [
                'id'             => 42,
                'lookup_type_id' => 4,
                'name'           => 'Multiple',
                'value'          => 'Multiple',
                'description'    => 'One to many relationship',
            ],
            [
                'id'             => 43,
                'lookup_type_id' => 4,
                'name'           => 'Scope',
                'value'          => 'Scope',
                'description'    => 'different scopes',
            ],
        ];

        foreach ( $records as $record )
            LookupValue::create($record);
    }

    /**
     * Drop the records that were created as part of the migration
     */
    public function drop ()
    {
        //delete the records of the lookup value
        $records = LookupValue::where('lookup_type_id', 4)->get();
        foreach ( $records as $record )
            $record->forceDelete();

        //delete the lookup definition itself
        $record = LookupType::find(4);
        $record->forceDelete();
    }
}
