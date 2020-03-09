<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\MailRecipientObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MailRecipient
 * @package Drivezy\LaravelRecordManager\Models
 */
class MailRecipient extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_mail_recipients';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new MailRecipientObserver());
    }

    /**
     * @return BelongsTo
     */
    public function mail ()
    {
        return $this->belongsTo(MailLog::class);
    }
}
