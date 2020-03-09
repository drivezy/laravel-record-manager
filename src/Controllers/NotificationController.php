<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\Notification;

/**
 * Class NotificationController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class NotificationController extends RecordController
{
    /**
     * @var string
     */
    protected $model = Notification::class;
}
