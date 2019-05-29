<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\SMSMessage;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class SMSManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class SMSManager {

    /**
     * @param $user
     * @param $content
     * @param array $attributes
     */
    public static function sendSmsToUser ($user, $content, $attributes = []) {
        //create user out of the user attribute.
        //if integer then create user object out of it
        $user = is_numeric($user) ? LaravelUtility::getUserModelFullQualifiedName()::find($user) : $user;

        //create message content in our db for transactional purpose
        $message = self::setMessage($user->mobile, $content, $attributes);

        //find the gateway through which sms is to be processed
        $gateway = isset($attributes['gateway']) ? $attributes['gateway'] : LaravelUtility::getProperty('sms.default.gateway');

        ( new $gateway($message) )->process();
    }

    /**
     * send sms to the mobile no directly without directly associating it with the user
     * @param $mobile
     * @param $content
     * @param array $attributes
     */
    public static function sendSmsToMobile ($mobile, $content, $attributes = []) {
        //create message content in our db for transactional purpose
        $message = self::setMessage($mobile, $content, $attributes);

        //find the gateway through which sms is to be processed
        $gateway = isset($attributes['gateway']) ? $attributes['gateway'] : LaravelUtility::getProperty('sms.default.gateway');

        ( new $gateway($message) )->process();
    }

    /**
     * @param $mobile
     * @param $content
     * @param array $attributes
     * @return SMSMessage
     */
    public static function setMessage ($mobile, $content, $attributes = []) {
        $sms = new SMSMessage();

        $sms->mobile = $mobile;
        $sms->content = $content;

        foreach ( $attributes as $key => $value ) {
            $sms->setAttribute($key, $value);
        }

        $sms->save();

        return $sms;
    }
}
