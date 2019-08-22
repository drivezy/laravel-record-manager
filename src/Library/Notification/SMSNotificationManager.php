<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\SMSNotification;

/**
 * Class SMSNotificationManager
 * @package Drivezy\LaravelRecordManager\src\Library\Notification
 */
class SMSNotificationManager extends BaseNotification {

    /**
     * process all sms notifications required for the given notification
     */
    public function process () {
        $this->processSmsNotifications();
    }

    /**
     * get all sms notification that is defined under this notification
     */
    private function processSmsNotifications () {
        $smsNotifications = SMSNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'template.gateway', 'run_condition'])->where('notification_id', $this->notification->id)->get();
        foreach ( $smsNotifications as $smsNotification ) {
            if ( $this->validateRunCondition($smsNotification->run_condition) && $smsNotification->active ) {
                $this->processSmsNotification($smsNotification);
            }
        }
    }

    /**
     * send individual sms notification to the targeted users
     * @param $smsNotification
     */
    private function processSmsNotification (SMSNotification $smsNotification) {
        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($smsNotification->default_users, $smsNotification->active_recipients);

        $content = $smsNotification->sms_template_id ? $smsNotification->template->content : $smsNotification->content;
        $gateway = $smsNotification->sms_template_id ? ( $smsNotification->template->gateway_id ? $smsNotification->template->gateway->description : null ) : null;

        $data = $this->data; //don't remove this

        foreach ( $users as $user ) {
            if ( !$this->validateSubscription($user, 'sms') ) continue;

            eval("\$content = \"$content\";");


            //check if the sms to be sent is a registered user
            if ( isset($user->id) && $user->mobile ) {
                SMSManager::sendSmsToUser($user, $content, ['gateway' => $gateway]);
                ++$this->sms_count;
            } elseif ( isset($user->mobile) ) {
                SMSManager::sendSmsToMobile($user->mobile, $gateway);
                ++$this->sms_count;
            }
        }
    }
}
