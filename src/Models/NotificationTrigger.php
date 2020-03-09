<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\NotificationTriggerObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class NotificationTrigger
 * @package Drivezy\LaravelRecordManager\Models
 */
class NotificationTrigger extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_notification_triggers';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new NotificationTriggerObserver());
    }

    /**
     * @return BelongsTo
     */
    public function notification ()
    {
        return $this->belongsTo(Notification::class);
    }
}
