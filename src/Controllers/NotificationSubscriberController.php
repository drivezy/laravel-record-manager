<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\NotificationSubscriber;

/**
 * Class NotificationSubscriberController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class NotificationSubscriberController extends RecordController {
    /**
     * @var string
     */
    protected $model = NotificationSubscriber::class;
}
