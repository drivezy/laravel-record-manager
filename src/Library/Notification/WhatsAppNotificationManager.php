<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Library\Notification\Templates\WhatsAppMessaging;
use Drivezy\LaravelRecordManager\Models\WhatsAppNotification;

/**
 * Class WhatsAppNotificationManager
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class WhatsAppNotificationManager extends BaseNotification
{
    /**
     *
     */
    public function process ()
    {
        //get all active whatsapp notifications defined under this
        $whatsAppNotifications = WhatsAppNotification::with([
            'active_recipients.custom_query',
            'active_recipients.run_condition',
            'template.gateway',
            'run_condition',
        ])->where('active', true)->where('notification_id', $this->notification->id)->get();

        foreach ( $whatsAppNotifications as $whatsAppNotification ) {
            //validate if the notification is validated for run condition
            if ( $this->validateRunCondition($whatsAppNotification->run_condition) ) {
                $this->processWhatsAppNotification($whatsAppNotification);
            }
        }
    }

    /**
     * @param WhatsAppNotification $whatsAppNotification
     */
    private function processWhatsAppNotification (WhatsAppNotification $whatsAppNotification)
    {
        $data = $this->data;

        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($whatsAppNotification->default_users, $whatsAppNotification->active_recipients);
        $content = $whatsAppNotification->template->content;

        preg_match_all("/{(.*?)}/", $content, $matches);

        //preparing content for all user recipients
        foreach ( $users as $user ) {
            if ( !$this->validateSubscription($user, 'whatsapp') ) continue;

            eval("\$content = \"$content\";");

            //create substitution parameter against user
            $parameters = [];
            foreach ( $matches[1] as $match ) {
                eval("\$match = \"$match\";");
                array_push($parameters, $match);
            }

            ( new WhatsAppManager($user) )->process($whatsAppNotification->template, $parameters, [
                'content'     => $content,
                'source_type' => $this->notification->data_model->model_hash,
                'source_id'   => $this->data->id,
            ]);
        }

    }
}