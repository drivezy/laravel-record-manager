<?php

namespace Drivezy\LaravelRecordManager\Library\Notification\Templates;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BaseMailable
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class BaseMailable extends Mailable {
    use Queueable, SerializesModels;
}
