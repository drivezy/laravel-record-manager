<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ClientScriptObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function script () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ClientScriptObserver());
    }
}