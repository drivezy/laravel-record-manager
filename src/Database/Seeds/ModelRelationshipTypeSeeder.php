<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\RelationshipDefinition;
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
        $columns = [
            [
                'name'        => 'Single',
                'description' => 'One to one relationship - belongsTo or has one',
            ],
            [
                'name'        => 'Multiple',
                'description' => 'One to many relationship - has many',
            ],
            [
                'name'        => 'Scope',
                'description' => 'System limiter on the query',
            ],
        ];

        foreach ( $columns as $column )
            RelationshipDefinition::firstOrCreate($column);
    }
}
