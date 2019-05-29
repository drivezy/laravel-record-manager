<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\SMSMessage;

/**
 * Interface SMSMessaging
 * @package Drivezy\LaravelRecordManager\Library
 */
interface SMSMessaging {
    /**
     * SMSMessaging constructor.
     * @param SMSMessage $message
     */
    public function __construct (SMSMessage $message);

    /**
     * @return mixed
     */
    public function process ();
}
