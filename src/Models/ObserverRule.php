<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ObserverRuleObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ObserverRule
 * @package Drivezy\LaravelRecordManager\Models
 */
class ObserverRule extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_observer_rules';

    /**
     * @return mixed
     */
    public function actions () {
        return $this->hasMany(ObserverAction::class);
    }

    /**
     * @return mixed
     */
    public function data_model () {
        return $this->belongsTo(DataModel::class, 'model_id');
    }

    /**
     * @return mixed
     */
    public function trigger_type () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return mixed
     */
    public function active_actions () {
        return $this->hasMany(ObserverAction::class)->where('active', true)->orderBy('execution_order', 'ASC');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive ($query) {
        return $query->where('active', true)->orderBy('execution_order', 'asc');
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ObserverRuleObserver());
    }
}
