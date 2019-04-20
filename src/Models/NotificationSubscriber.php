<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\NotificationSubscriberObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class NotificationSubscriber
 * @package Drivezy\LaravelRecordManager\Models
 */
class NotificationSubscriber extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_notification_subscribers';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification () {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new NotificationSubscriberObserver());
    }
}
