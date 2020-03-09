<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\DeviceTokenObserver;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DeviceToken
 * @package Drivezy\LaravelRecordManager\Models
 */
class DeviceToken extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_device_tokens';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new DeviceTokenObserver());
    }

    /**
     * @return BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo(LaravelUtility::getUserModelFullQualifiedName());
    }
}
