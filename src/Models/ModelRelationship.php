<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAdmin\Models\UIAction;
use Drivezy\LaravelRecordManager\Observers\ModelRelationshipObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ModelRelationship
 * @package Drivezy\LaravelRecordManager\Models
 */
class ModelRelationship extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_model_relationships';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new ModelRelationshipObserver());
    }

    /**
     * @return BelongsTo
     */
    public function model ()
    {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return BelongsTo
     */
    public function source_column ()
    {
        return $this->belongsTo(Column::class);
    }

    /**
     * @return BelongsTo
     */
    public function alias_column ()
    {
        return $this->belongsTo(Column::class);
    }

    /**
     * @return BelongsTo
     */
    public function reference_type ()
    {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return BelongsTo
     */
    public function reference_model ()
    {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return HasMany
     */
    public function ui_actions ()
    {
        return $this->hasMany(UIAction::class, 'source_id')->where('source_type', md5(self::class));
    }

}
