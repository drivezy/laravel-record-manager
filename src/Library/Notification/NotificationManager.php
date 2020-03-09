<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\Notification;
use Drivezy\LaravelUtility\Library\DateUtil;
use Log;

/**
 * Class NotificationManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class NotificationManager
{
    private $notification;

    /**
     * NotificationManager constructor.
     * @param $notificationId
     */
    public function __construct ($notificationId)
    {
        $this->notification = Notification::with(['custom_data', 'data_model', 'run_condition'])->find($notificationId);
    }

    /**
     * handle the notification process
     * @param $id
     * @return bool|mixed
     */
    public function process ($id = null)
    {
        Log::info('i am here : ' . DateUtil::getDateTime());
        //validate if the notification is present or not
        if ( !$this->notification ) return false;

        //validate if we have disabled the given notification
        if ( !$this->notification->active ) return false;

        //get the data required for the given notification
        //if no data then simply exit the process
        $data = $this->prepareNotificationData($id);
        if ( !$data ) return false;

        if ( !BaseNotification::validateCondition($this->notification->run_condition, $data) ) return false;

        $users = ( new NotificationUserManager([
            'notification' => $this->notification,
            'data'         => $data,
        ]) )->getNotificationUsers($this->notification->active_recipients);

        //create arguments for the notification base
        $args = [
            'notification'  => $this->notification,
            'data'          => $data,
            'default_users' => $users,
        ];

        //sending notifications to all segments
        ( new SMSNotificationManager($args) )->process();
        ( new WhatsAppNotificationManager($args) )->process();
        ( new EmailNotificationManager($args) )->process();
        ( new PushNotificationManager($args) )->process();
        ( new InAppNotificationManager($args) )->process();

        return true;
    }

    /**
     * create notification data from custom script and basic base model object with includes
     * @param $id
     * @return mixed
     */
    private function prepareNotificationData ($id)
    {
        if ( !( $this->notification->data_model_id && $id ) ) return false;

        $class = $this->notification->data_model->namespace . '\\' . $this->notification->data_model->name;

        $includes = $this->sanitizeIncludes($this->notification->includes ? explode(',', $this->notification->includes) : []);
        $data = $class::with($includes)->find($id);

        if ( !$data ) return false;

        if ( $this->notification->custom_data_id ) {
            $script = $this->notification->custom_data->script;
            eval($script);
        }

        return $data;
    }


    /**
     * remove spaces in-between the include statements
     * @param $arr
     * @return array
     */
    private function sanitizeIncludes ($arr)
    {
        $sanitized = [];
        foreach ( $arr as $item )
            array_push($sanitized, trim($item));

        return $sanitized;
    }
}
