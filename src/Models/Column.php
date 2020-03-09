<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ColumnObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Column
 * @package Drivezy\LaravelRecordManager\Models
 */
class Column extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_column_details';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'source_type', 'source_id'];

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new ColumnObserver());
    }

    /**
     * @return BelongsTo
     */
    public function reference_model ()
    {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return BelongsTo
     */
    public function reference_column ()
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return BelongsTo
     */
    public function column_type ()
    {
        return $this->belongsTo(ColumnDefinition::class);
    }

    /**
     * @return HasMany
     */
    public function security_rules ()
    {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', md5(self::class));
    }
}
