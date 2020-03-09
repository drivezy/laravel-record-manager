<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\MailRecipient;

/**
 * Class MailRecipientController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class MailRecipientController extends ReadRecordController
{

    /**
     * @var string
     */
    protected $model = MailRecipient::class;
}

