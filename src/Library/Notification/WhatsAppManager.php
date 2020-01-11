<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Library\Notification\Templates\WhatsAppFileMessaging;
use Drivezy\LaravelRecordManager\Models\WhatsAppMessage;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class WhatsAppManager
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class WhatsAppManager
{
    /**
     * @var null
     */
    private $user = null;
    /**
     * @var bool|string
     */
    private $live = false;

    /**
     * WhatsAppManager constructor.
     * @param $user
     */
    public function __construct ($user)
    {
        $this->user = $user;
        $this->live = LaravelUtility::getProperty('whatsapp.live.enable', false);

        $this->setUserData();
    }

    /**
     * @param $template
     * @param $params
     * @param array $attributes
     */
    public function process ($template, $params, $attributes = [])
    {
        //if a user is found, assign it to the message
        if ( isset($this->user->id) ) $attributes['user_id'] = $this->user->id;

        //setup a record of the message in our system
        $message = $this->setWhatsAppMessage($attributes);
        $gateway = $template->gateway->description;

        //check if we have enabled whatsapp live on this environment and the gateway exists
        if ( !( $this->live && class_exists($gateway) ) )
            return ( new WhatsAppFileMessaging($message) )->process();

        //if not then send it to the whatsapp gateway

        ( new $gateway($message) )->process($template->identifier, $params);
    }

    /**
     * get user record is numeric value is passed against the user
     * and then set user record against the data.
     */
    private function setUserData ()
    {
        //create user out of the user attribute.
        //if integer then create user object out of it
        $userModel = LaravelUtility::getUserModelFullQualifiedName();
        $this->user = is_numeric($this->user) ? $userModel::find($this->user) : $this->user;
    }

    /**
     * @param array $attributes
     * @return WhatsAppMessage
     */
    private function setWhatsAppMessage ($attributes = [])
    {
        $message = new WhatsAppMessage();

        $message->mobile = $this->user->mobile;
        foreach ( $attributes as $key => $value )
            $message->{$key} = $value;

        $message->save();

        return $message;
    }
}