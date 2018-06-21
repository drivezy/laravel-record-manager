<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\UIActionObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class UIAction
 * @package Drivezy\LaravelRecordManager\Models
 */
class UIAction extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_ui_actions';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filter_condition () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function execution_script () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form () {
        return $this->belongsTo(CustomForm::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new UIActionObserver());
    }
}