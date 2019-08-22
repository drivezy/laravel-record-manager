<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class MailLogObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class MailRecipientObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'mail_id' => 'required',
    ];
}
