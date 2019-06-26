<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\NotificationTriggerObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class NotificationTrigger
 * @package Drivezy\LaravelRecordManager\Models
 */
class NotificationTrigger extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_notification_triggers';

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
        self::observe(new NotificationTriggerObserver());
    }

}
