<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ModelColumnObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ModelColumn
 * @package Drivezy\LaravelRecordManager\Models
 */
class ModelColumn extends BaseModel {

    /**
     * @var string
     */
    protected $table = 'dz_model_columns';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model () {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function column_type () {
        return $this->belongsTo(ColumnDefinition::class);
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
    public function security_rules () {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function read_security_rules () {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', self::class)->where('operation', 'r');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function write_security_rules () {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', self::class)->whereIn('operation', ['r', 'w']);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelColumnObserver());
    }
}