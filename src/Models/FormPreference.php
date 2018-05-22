<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\FormPreferenceObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class FormPreference
 * @package Drivezy\LaravelRecordManager\Models
 */
class FormPreference extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_form_preferences';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new FormPreferenceObserver());
    }

}