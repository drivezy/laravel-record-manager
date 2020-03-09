<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ColumnDefinitionObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ColumnDefinition
 * @package Drivezy\LaravelRecordManager\Models
 */
class ColumnDefinition extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_column_definitions';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new ColumnDefinitionObserver());
    }
}
