<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ScriptTypeObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ScriptType
 * @package Drivezy\LaravelRecordManager\Models
 */
class ScriptType extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_script_types';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ScriptTypeObserver());
    }

}