<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ColumnObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class Column
 * @package Drivezy\LaravelRecordManager\Models
 */
class Column extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_column_details';
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'source_type', 'source_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference_model () {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function column_type () {
        return $this->belongsTo(ColumnDefinition::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function security_rules () {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ColumnObserver());
    }
}