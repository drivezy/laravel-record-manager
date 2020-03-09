<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Observers\ListPreferenceObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListPreference
 * @package Drivezy\LaravelRecordManager\Models
 */
class ListPreference extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_list_preferences';
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'source_type', 'source_id'];

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new ListPreferenceObserver());
    }

    /**
     * @return BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo(AccessManager::getUserClass());
    }
}
