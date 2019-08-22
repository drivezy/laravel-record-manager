<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\MailLogObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class MailLog
 * @package Drivezy\LaravelRecordManager\Models
 */
class MailLog extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_mail_logs';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mail_recipients () {
        return $this->hasMany(MailRecipient::class, 'mail_id');
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new MailLogObserver());
    }
}
