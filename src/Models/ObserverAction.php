<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ObserverActionObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ObserverAction
 * @package Drivezy\LaravelRecordManager\Models
 */
class ObserverAction extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_observer_actions';

    /**
     * @return mixed
     */
    public function observer_rule () {
        return $this->belongsTo(ObserverRule::class);
    }

    /**
     * @return mixed
     */
    public function script () {
        return $this->belongsTo(SystemScript::class);
    }


    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ObserverActionObserver());
    }
}
