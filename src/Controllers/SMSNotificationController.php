<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\SMSNotification;

/**
 * Class SMSNotificationController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class SMSNotificationController extends RecordController
{
    /**
     * @var string
     */
    protected $model = SMSNotification::class;
}
