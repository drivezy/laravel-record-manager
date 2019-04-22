<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\SMSTemplateObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class SMSTemplate
 * @package Drivezy\LaravelRecordManager\Models
 */
class SMSTemplate extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_sms_templates';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sms_notifications () {
        return $this->hasMany(SMSNotification::class, 'template_id');
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new SMSTemplateObserver());
    }

}
