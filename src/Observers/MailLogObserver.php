<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class MailLogObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class MailLogObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'primary_recipient' => 'required',
        'subject'           => 'required',
    ];
}
