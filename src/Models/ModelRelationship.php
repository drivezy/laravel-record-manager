<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ModelRelationshipObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ModelRelationship
 * @package Drivezy\LaravelRecordManager\Models
 */
class ModelRelationship extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_model_relationships';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model () {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source_column () {
        return $this->belongsTo(Column::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alias_column () {
        return $this->belongsTo(Column::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference_type () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference_model () {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ui_actions () {
        return $this->hasMany(UIAction::class, 'source_id')->where('source_type', self::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelRelationshipObserver());
    }

}