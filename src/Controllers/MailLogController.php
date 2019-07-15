<?php

namespace Drivezy\LaravelRecordManager\Controllers;


use Drivezy\LaravelRecordManager\Models\MailLog;

/**
 * Class MailLogController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class MailLogController extends ReadRecordController {

    /**
     * @var string
     */
    protected $model = MailLog::class;
}

