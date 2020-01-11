<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\WhatsAppNotification;

/**
 * Class WhatsAppNotificationController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class WhatsAppNotificationController extends RecordController
{
    /**
     * @var string
     */
    protected $model = WhatsAppNotification::class;
}