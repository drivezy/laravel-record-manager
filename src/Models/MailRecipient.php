<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\MailRecipientObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class MailRecipient
 * @package Drivezy\LaravelRecordManager\Models
 */
class MailRecipient extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_mail_recipients';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mail () {
        return $this->belongsTo(MailLog::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new MailRecipientObserver());
    }
}
