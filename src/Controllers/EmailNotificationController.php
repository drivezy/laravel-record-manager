<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\EmailNotification;

/**
 * Class EmailNotificationController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class EmailNotificationController extends RecordController
{
    /**
     * @var string
     */
    protected $model = EmailNotification::class;
}
