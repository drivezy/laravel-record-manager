<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ModelObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class Model
 * @package Drivezy\LaravelRecordManager\Models
 */
class Model extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_model_details';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelObserver());
    }
}