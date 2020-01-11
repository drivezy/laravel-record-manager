<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Library\Notification\Templates\WhatsAppMessaging;
use Drivezy\LaravelRecordManager\Models\SMSNotification;

/**
 * Class SMSNotificationManager
 * @package Drivezy\LaravelRecordManager\src\Library\Notification
 */
class SMSNotificationManager extends BaseNotification
{

    /**
     * process all sms notifications required for the given notification
     */
    public function process ()
    {
        //get all sms notifications defined under this notification
        $smsNotifications = SMSNotification::with([
            'active_recipients.custom_query',
            'active_recipients.run_condition',
            'template.gateway',
            'run_condition',
        ])->where('active', true)->where('notification_id', $this->notification->id)->get();

        foreach ( $smsNotifications as $smsNotification ) {
            //check for the run condition
            if ( $this->validateRunCondition($smsNotification->run_condition) ) {
                $this->processSmsNotification($smsNotification);
            }
        }
    }

    /**
     * send individual sms notification to the targeted users
     * this also supports whatsapp notification wherein the gateway is whatsapp
     * @param $smsNotification
     */
    private function processSmsNotification (SMSNotification $smsNotification)
    {
        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($smsNotification->default_users, $smsNotification->active_recipients);

        $content = $smsNotification->template->content;
        $gateway = $smsNotification->template->gateway->description;

        $data = $this->data; //don't remove this

        foreach ( $users as $user ) {
            if ( !$this->validateSubscription($user, 'sms') ) continue;

            eval("\$content = \"$content\";");

            ( new SMSManager($user) )->process($gateway, $content, [
                'source_type' => $this->notification->data_model->model_hash,
                'source_id'   => $this->data->id,
            ]);
        }
    }
}
