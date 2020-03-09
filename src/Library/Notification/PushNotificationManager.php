<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\DeviceToken;
use Drivezy\LaravelRecordManager\Models\PushNotification;
use Illuminate\Support\Facades\DB;

class PushNotificationManager extends BaseNotification
{

    /**
     * process all push notifications required for the given notification
     */
    public function process ()
    {
        $this->processPushNotifications();
    }

    /**
     * process push notifications
     * @return mixed
     */
    private function processPushNotifications ()
    {
        $pushNotifications = PushNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'run_condition', 'custom_query'])
            ->where('notification_id', $this->notification->id)->get();
        foreach ( $pushNotifications as $pushNotification ) {
            if ( $this->validateRunCondition($pushNotification->run_condition) ) {
                if ( $pushNotification->active )
                    $this->processPushNotification($pushNotification);
            }
        }
    }

    /**
     * send out push notification to the intended users
     * @param $pushNotification
     * @return mixed
     */
    private function processPushNotification ($pushNotification)
    {
        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($pushNotification->default_users, $pushNotification->active_recipients);
        $devices = $this->getPushNotificationDevices($users, $pushNotification);

        if ( !sizeof($devices) ) return true;

        $data = $this->notification_data;
        $notificationObject = [];

        foreach ( $pushNotification->notification_object as $key => $value ) {
            eval("\$value = \"$value\";");
            $notificationObject[ $key ] = $value;
        }

        $params = [
            'content_available' => true,
            'delay_while_idle'  => false,
            'notification'      => [
                'sound' => 'chime',
                'icon'  => 'ic_push',
            ],
            'registration_ids'  => $devices,
            'priority'          => 'high',
            'time_to_live'      => 3600 * 10,
            'notification'      => $notificationObject,
            'data'              => $pushNotification->data_object,
        ];

        $this->push_count += sizeof($devices);

        return FirebaseUtil::sendPushNotification($params);
    }

    /**
     * Get registered device against push notification
     * @param $users
     * @param $pushNotification
     * @return mixed
     */
    protected function getPushNotificationDevices ($users, $pushNotification)
    {
        $data = $this->data;

        //get all users who are eligible for the push notification
        $arr = [];
        foreach ( $users as $user ) {
            if ( isset($user->id) ) {
                //check if the user has unsubscribed for the push notification
                if ( !$this->validateSubscription($user, 'push') ) continue;

                array_push($arr, $user->id);
            }
        }

        //get all devices which are registered against that platform for the user
        $devices = DeviceToken::whereIn('user_id', $arr);
        if ( $pushNotification->target_devices ) {
            $targetDevices = [];
            foreach ( $pushNotification->target_devices as $device )
                array_push($targetDevices, $device->id);

            $devices = $devices->whereIn('token_source_id', $targetDevices);
        }

        $devices = $devices->pluck('token')->toArray();
        if ( !$pushNotification->query ) return $devices;

        if ( $pushNotification->query_id ) {
            $query = $pushNotification->custom_query->script;
            eval("\$query = \"$query\";");

            $rows = DB::select(DB::raw($query));
            foreach ( $rows as $row ) {
                if ( !in_array($row->token, $devices) )
                    array_push($devices, self::getUserObject($row->token));
            }
        }

        return $devices;
    }


}
