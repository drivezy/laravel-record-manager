<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\PushNotification;

/**
 * Class PushNotificationController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class PushNotificationController extends RecordController {
    /**
     * @var string
     */
    protected $model = PushNotification::class;
}
