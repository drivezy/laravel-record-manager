<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\CustomFormObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class CustomForm
 * @package Drivezy\LaravelRecordManager\Models
 */
class CustomForm extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_custom_forms';
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new CustomFormObserver());
    }
}