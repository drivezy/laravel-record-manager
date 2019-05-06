<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\SMSMessageObserver;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class SMSMessage
 * @package Drivezy\LaravelRecordManager\Models
 */
class SMSMessage extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_sms_messages';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user () {
        return $this->belongsTo(LaravelUtility::getUserModelFullQualifiedName());
    }

    /**
     * over riding the boot functionality
     */
    protected static function boot () {
        parent::boot();
        self::observe(new SMSMessageObserver());
    }
}
