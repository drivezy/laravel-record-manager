<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class WhatsAppNotificationObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class WhatsAppNotificationObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'notification_id' => 'required',
        'template_id'     => 'required',
    ];
}