<?php

namespace Drivezy\LaravelRecordManager\Models;

use App\User;
use Drivezy\LaravelRecordManager\Observers\ListPreferenceObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ListPreference
 * @package Drivezy\LaravelRecordManager\Models
 */
class ListPreference extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_list_preferences';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user () {
        return $this->belongsTo(User::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ListPreferenceObserver());
    }
}