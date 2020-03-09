<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\SMSTemplateObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SMSTemplate
 * @package Drivezy\LaravelRecordManager\Models
 */
class SMSTemplate extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_sms_templates';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new SMSTemplateObserver());
    }

    /**
     * @return BelongsTo
     */
    public function gateway ()
    {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return HasMany
     */
    public function sms_notifications ()
    {
        return $this->hasMany(SMSNotification::class, 'sms_template_id');
    }

}
