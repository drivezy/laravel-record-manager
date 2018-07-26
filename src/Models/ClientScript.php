<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ClientScriptObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class ClientScript
 * @package Drivezy\LaravelRecordManager\Models
 */
class ClientScript extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_client_scripts';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function script () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity_type () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ClientScriptObserver());
    }
}