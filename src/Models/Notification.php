<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\NotificationObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\EventDetail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Notification
 * @package Drivezy\LaravelRecordManager\Models
 */
class Notification extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_notification_details';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new NotificationObserver());
    }

    /**
     * @return BelongsTo
     */
    public function data_model ()
    {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return BelongsTo
     */
    public function custom_data ()
    {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return BelongsTo
     */
    public function run_condition ()
    {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return HasMany
     */
    public function recipients ()
    {
        return $this->hasMany(NotificationRecipient::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return HasMany
     */
    public function active_recipients ()
    {
        return $this->hasMany(NotificationRecipient::class, 'source_id')->where('source_type', md5(self::class))->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function sms_notifications ()
    {
        return $this->hasMany(SMSNotification::class, 'notification_id');
    }

    /**
     * @return HasMany
     */
    public function active_sms_notifications ()
    {
        return $this->hasMany(SMSNotification::class, 'notification_id')->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function email_notifications ()
    {
        return $this->hasMany(EmailNotification::class, 'notification_id');
    }

    /**
     * @return HasMany
     */
    public function active_email_notifications ()
    {
        return $this->hasMany(EmailNotification::class, 'notification_id')->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function push_notifications ()
    {
        return $this->hasMany(PushNotification::class, 'notification_id');
    }

    /**
     * @return HasMany
     */
    public function active_push_notifications ()
    {
        return $this->hasMany(PushNotification::class, 'notification_id')->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function inapp_notifications ()
    {
        return $this->hasMany(InAppNotification::class, 'notification_id');
    }

    /**
     * @return HasMany
     */
    public function active_inapp_notifications ()
    {
        return $this->hasMany(InAppNotification::class, 'notification_id')->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function whatsapp_notifications ()
    {
        return $this->hasMany(WhatsAppNotification::class);
    }

    /**
     * @return HasMany
     */
    public function active_whatsapp_notifications ()
    {
        return $this->hasMany(WhatsAppNotification::class)->where('active', true);
    }

    /**
     * @return HasMany
     */
    public function triggers ()
    {
        return $this->hasMany(NotificationTrigger::class);
    }

    /**
     * @return HasMany
     */
    public function event_details ()
    {
        return $this->hasMany(EventDetail::class);
    }

}
