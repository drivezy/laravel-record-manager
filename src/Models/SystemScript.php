<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\SystemScriptObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class SystemScript
 * @package Drivezy\LaravelRecordManager\Models
 */
class SystemScript extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_system_scripts';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new SystemScriptObserver());
    }

}