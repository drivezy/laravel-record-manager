<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\RelationshipDefinitionObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class RelationshipDefinition
 * @package Drivezy\LaravelRecordManager\Models
 */
class RelationshipDefinition extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_relationship_definitions';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new RelationshipDefinitionObserver());
    }
}