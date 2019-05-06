<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\EmailNotification;
use Drivezy\LaravelRecordManager\Models\Notification;
use Drivezy\LaravelRecordManager\Models\SMSNotification;
use Drivezy\LaravelUtility\LaravelUtility;

class NotificationManager extends NotificationRecipientManager {
    /**
     * NotificationManager constructor.
     * @param $notificationId
     */
    public function __construct ($notificationId) {
        $this->notification = Notification::with(['custom_data', 'data_model', 'run_condition'])->find($notificationId);

        parent::__construct();
    }

    /**
     * handle the notification process
     * @param $id
     * @return bool|mixed
     */
    public function processNotification ($id = null) {
        if ( !$this->notification->active ) return false;

        dd($this->notification);

        $result = $this->prepareNotificationData($id);
        if ( !$result['success'] ) return false;

        $this->notification_data = $result['data'];
        CustomLogging::info($this->notification_data);

        if ( !$this->notification_data ) return false;

        if ( !$this->validateRunCondition($this->notification->run_condition) ) return false;
        CustomLogging::info('Successfully Passed Run Condition of Notification');

        $this->default_users = $this->getNotificationUsers($this->notification->active_recipients);
        CustomLogging::info('Default Users');
        CustomLogging::info('Default Users Count : ' . sizeof($this->default_users));

        $this->processEmailNotifications();
        $this->processSmsNotifications();
        $this->processPushNotifications();

        return true;
    }

    /**
     * create notification data from custom script and basic base model object with includes
     * @param $id
     * @return mixed
     */
    private function prepareNotificationData ($id) {
        if ( !( $this->notification->model_id && $id ) ) return ['success' => false];

        $class = $this->notification->data_model->namespace . '\\' . $this->notification->data_model->name;

        $includes = $this->sanitizeIncludes($this->notification->includes ? explode(',', $this->notification->includes) : []);
        $data = $class::with($includes)->find($id);

        if ( !$data ) return ['success' => false];
        if ( !$this->validateRunCondition($this->notification->pre_run_condition, $data) ) return ['success' => false];

        if ( $this->notification->custom_data_id ) {
            $script = $this->notification->custom_data->script;
//            SentryLogging::set('notification.custom.data', $script);
            eval($script);
        }

        return ['success' => true, 'data' => $data];
    }

    /**
     * push email notification
     */
    private function processEmailNotifications () {
        CustomLogging::info('Starting processing of email notifications');

        $emailNotifications = EmailNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'run_condition', 'body'])->where('notification_id', $this->notification->id)->get();
        foreach ( $emailNotifications as $emailNotification ) {
            if ( $this->validateRunCondition($emailNotification->run_condition) ) {
                if ( $emailNotification->active )
                    $this->processEmailNotification($emailNotification);
            } else {
                CustomLogging::info('Email Notification ' . $emailNotification->name . ' Failed Run Condition');
            }
        }
    }

    /**
     * send individual email notification to users
     * @param $emailNotification
     */
    private function processEmailNotification ($emailNotification) {
        CustomLogging::info('Processing Email Notification ' . $emailNotification->name);
        $users = $this->getTotalUsers($emailNotification->default_users, $emailNotification->active_recipients);

        $subject = LaravelUtility::parseBladeToString($emailNotification->subject, $this->notification_data);
        CustomLogging::info('Subject is ' . $subject);

        $body = $emailNotification->body_id ? $emailNotification->body->script : '';
        $body = LaravelUtility::parseBladeToString($body, $this->notification_data);

        $mailable = new NotificationMailMessage($emailNotification->template_name, $subject, $body, $this->notification_data);

        foreach ( $users as $user ) {
            if ( !filter_var($user->email, FILTER_VALIDATE_EMAIL) ) {
                CustomLogging::info('Failed email for ' . $user->email);
                continue;
            }

            if ( !$this->validateSubscription($user, 'email') )
                continue;

            CustomLogging::info('Sent email to ' . $user->email);
            Mail::to($user)->send($mailable);
            ++$this->email_count;
        }
    }

    /**
     * process sms notification
     */
    private function processSmsNotifications () {
        CustomLogging::info('Starting processing of sms notifications');

        $smsNotifications = SMSNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'template.gateway', 'run_condition'])->where('notification_id', $this->notification->id)->get();
        foreach ( $smsNotifications as $smsNotification ) {
            if ( $this->validateRunCondition($smsNotification->run_condition) ) {
                if ( $smsNotification->active )
                    $this->processSmsNotification($smsNotification);
            } else {
                CustomLogging::info('Run condition failed for SMS notification ' . $smsNotification->name);
            }
        }

    }

    /**
     * send individual sms notification to the targeted users
     * @param $smsNotification
     */
    private function processSmsNotification ($smsNotification) {
        CustomLogging::info('processing sms notification ' . $smsNotification->name);

        $users = $this->getTotalUsers($smsNotification->default_users, $smsNotification->active_recipients);

        $content = $smsNotification->template_id ? $smsNotification->template->content : $smsNotification->content;
        $gateway = $smsNotification->template_id ? $smsNotification->template->gateway->description : null;

        $data = $this->notification_data; //don't remove this
//        SentryLogging::set('sms.notification.data.' . $smsNotification->id, $content);
        eval("\$content = \"$content\";");

        CustomLogging::info('Content is : ' . $content);
        CustomLogging::info('gateway is : ' . $gateway);

        foreach ( $users as $user ) {
            if ( isset($user->id) ) {
                SMSManager::sendSmsToUser($user, $content, $gateway);
                CustomLogging::info('processed sms for ' . $user->mobile);
                ++$this->sms_count;
            } elseif ( isset($user->mobile) ) {
                SMSManager::sendSmsToMobile($user->mobile, $content, $gateway);
                CustomLogging::info('processed sms for ' . $user->mobile);
                ++$this->sms_count;
            }

        }
    }

    /**
     * process push notifications
     * @return mixed
     */
    private function processPushNotifications () {
        CustomLogging::info('Starting to process push notifications');

        $pushNotifications = PushNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'run_condition', 'custom_query'])->where('notification_id', $this->notification->id)->get();
        foreach ( $pushNotifications as $pushNotification ) {
            if ( $this->validateRunCondition($pushNotification->run_condition) ) {
                if ( $pushNotification->active )
                    $this->processPushNotification($pushNotification);
            } else {
                CustomLogging::info('Push notification failed run condition ' . $pushNotification->name);
            }
        }
    }

    /**
     * send out push notification to the intended users
     * @param $pushNotification
     * @return mixed
     */
    private function processPushNotification ($pushNotification) {
        CustomLogging::info('Processing push notification ' . $pushNotification->name);
        $users = $this->getTotalUsers($pushNotification->default_users, $pushNotification->active_recipients);
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

        //save all notifications in notification table, irrespective of delivery.
        foreach ( $users as $user ) {
            $pushNotificationData = new PushNotificationTrigger();
            $pushNotificationData->user_id = $user->id;
            $pushNotificationData->notification_id = $pushNotification->notification_id;
            $pushNotificationData->notification_data_object = serialize(['data' => $pushNotification->data_object, 'notification' => $notificationObject]);
            $pushNotificationData->image_url = ( $pushNotification->image_url ) ? $pushNotification->image_url : '';
            $pushNotificationData->deep_link_url = $pushNotification->click_action;
            $pushNotificationData->save();
        }


        $this->push_count += sizeof($devices);

        CustomLogging::info('pushed to devices ' . sizeof($devices));

        return FirebaseUtil::sendPushNotification($params);
    }

    /**
     * remove spaces in-between the include statements
     * @param $arr
     * @return array
     */
    private function sanitizeIncludes ($arr) {
        $sanitized = [];
        foreach ( $arr as $item )
            array_push($sanitized, trim($item));

        return $sanitized;
    }

    /**
     *
     */
    public function __destruct () {
        parent::__destruct();
    }
}
