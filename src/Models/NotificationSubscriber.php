<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\NotificationSubscriberObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class NotificationSubscriber
 * @package Drivezy\LaravelRecordManager\Models
 */
class NotificationSubscriber extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_notification_subscriptions';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new NotificationSubscriberObserver());
    }

    /**
     * @return BelongsTo
     */
    public function notification ()
    {
        return $this->belongsTo(Notification::class);
    }
}
