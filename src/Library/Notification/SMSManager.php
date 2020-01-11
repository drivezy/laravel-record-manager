<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Library\Notification\Templates\FileSMSMessaging;
use Drivezy\LaravelRecordManager\Models\SMSMessage;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class SMSManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class SMSManager
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
     * SMSManager constructor.
     * @param $user
     */
    public function __construct ($user)
    {
        $this->user = $user;
        $this->live = LaravelUtility::getProperty('sms.live.enable', false);

        $this->setUserData();
    }

    /**
     * @param $gateway
     * @param $content
     * @param array $attributes
     */
    public function process ($gateway, $content, $attributes = [])
    {
        //if a user is found, assign it to the message
        if ( isset($this->user->id) ) $attributes['user_id'] = $this->user->id;
        $attributes['gateway'] = $gateway;

        $message = $this->setMessage($content, $attributes);

        //check if we have enabled sms live on this environment
        if ( !( $this->live && class_exists($gateway) ) )
            return ( new FileSMSMessaging($message) )->process();

        ( new $gateway($message) )->process();
    }

    /**
     *
     */
    private function setUserData ()
    {
        //create user out of the user attribute.
        //if integer then create user object out of it
        $userModel = LaravelUtility::getUserModelFullQualifiedName();
        $this->user = is_numeric($this->user) ? $userModel::find($this->user) : $this->user;
    }

    /**
     * @param $content
     * @param array $attributes
     * @return SMSMessage
     */
    public function setMessage ($content, $attributes = [])
    {
        $sms = new SMSMessage();

        $sms->mobile = $this->user->mobile;
        $sms->content = $content;

        foreach ( $attributes as $key => $value ) {
            $sms->setAttribute($key, $value);
        }

        $sms->save();

        return $sms;
    }
}
