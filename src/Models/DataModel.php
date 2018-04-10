<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ModelObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class Model
 * @package Drivezy\LaravelRecordManager\Models
 */
class DataModel extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_model_details';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function columns () {
        return $this->hasMany(ModelColumn::class, 'model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relationships () {
        return $this->hasMany(ModelRelationship::class, 'model_id');
    }


    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelObserver());
    }
}