<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\PushNotificationObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class PushNotification
 * @package Drivezy\LaravelRecordManager\Models
 */
class PushNotification extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_push_notifications';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification () {
        return $this->belongsTo(Notification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification_object () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function data_object () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @param $str
     * @return |null
     */
    public function getTargetDevicesAttribute ($str) {
        if ( !$str ) return null;

        return LookupValue::whereIn('id', explode(',', $str))->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients () {
        return $this->hasMany(NotificationRecipient::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function active_recipients () {
        return $this->hasMany(NotificationRecipient::class, 'source_id')->where('source_type', md5(self::class))->where('active', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function run_condition () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custom_query () {
        return $this->belongsTo(SystemScript::class, 'query_id');
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new PushNotificationObserver());
    }

}
