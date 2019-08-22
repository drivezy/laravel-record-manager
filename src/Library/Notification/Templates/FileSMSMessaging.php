<?php

namespace Drivezy\LaravelRecordManager\Library\Notification\Templates;

use Drivezy\LaravelRecordManager\Models\SMSMessage;
use Illuminate\Support\Facades\Log;

/**
 * Class FileSMSMessaging
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class FileSMSMessaging {
    /**
     * @var SMSMessage|null
     */
    protected $message = null;

    /**
     * FileSMSMessaging constructor.
     * @param SMSMessage $message
     */
    public function __construct (SMSMessage $message) {
        $this->message = $message;
    }

    /**
     * process sms against the given user
     */
    public function process () {
        Log::info('############# Start of SMS Message ##################');
        Log::info('User : ' . $this->message->mobile);
        Log::info('Message : ' . $this->message->content);
        Log::info('############# End of SMS Message ##################');
    }
}
