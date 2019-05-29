<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\SMSMessage;

/**
 * Class SMSMessageController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class SMSMessageController extends ReadRecordController {
    /**
     * @var string
     */
    protected $model = SMSMessage::class;
}
