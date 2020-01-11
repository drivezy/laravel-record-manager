<?php

namespace Drivezy\LaravelRecordManager\Library\Notification\Templates;

use Drivezy\LaravelRecordManager\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Log;

/**
 * Class WhatsAppFileMessaging
 * @package Drivezy\LaravelRecordManager\Library\Notification\Templates
 */
class WhatsAppFileMessaging
{
    /**
     * @var WhatsAppMessage|null
     */
    protected $message = null;

    /**
     * WhatsAppFileMessaging constructor.
     * @param WhatsAppMessage $message
     */
    public function __construct (WhatsAppMessage $message)
    {
        $this->message = $message;
    }

    /**
     * process sms against the given user
     */
    public function process ()
    {
        Log::info('############# Start of WhatsApp Message ##################');
        Log::info('User : ' . $this->message->mobile);
        Log::info('Message : ' . $this->message->content);
        Log::info('############# End of WhatsApp Message ##################');

        //save the gateway of the class to the same one
        $this->message->gateway = self::class;
        $this->message->save();
    }

    public function callback ()
    {
        return $this->message;
    }
}