<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\InAppMessage;
use Drivezy\LaravelRecordManager\Models\InAppNotification;
use Drivezy\LaravelUtility\Library\DateUtil;

/**
 * Class InAppNotificationManager
 * @package Drivezy\LaravelRecordManager\src\Library\Notification
 */
class InAppNotificationManager extends BaseNotification
{

    /**
     * process all inapp notifications which are part of it
     */
    public function process ()
    {
        $this->processInAppNotifications();
    }

    /**
     *
     */
    private function processInAppNotifications ()
    {
        $inAppNotifications = InAppNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'template.gateway', 'run_condition'])->where('notification_id', $this->notification->id)->get();
        foreach ( $inAppNotifications as $inAppNotification ) {
            if ( $this->validateRunCondition($inAppNotification->run_condition) ) {
                if ( $inAppNotification->active )
                    $this->processInAppNotification($inAppNotification);
            }
        }
    }

    /**
     * @param InAppNotification $inAppNotification
     */
    private function processInAppNotification (InAppNotification $inAppNotification)
    {
        //get all users who are defined for the given notification
        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($inAppNotification->default_users, $inAppNotification->active_recipients);

        $data = $this->data;

        //initialize the variable
        $content = $description = $deep_link_url = null;

        foreach ( $users as $user ) {
            //validate if the user has unsubscribed against the given notification
            if ( !$this->validateSubscription($user, 'in_app') )
                continue;

            //dont send the inapp notification to the undefined user
            if ( !isset($user->id) ) continue;

            //parse the content in context of the user as well
            eval("\$content = \"$inAppNotification->content\";");
            eval("\$description = \"$inAppNotification->description\";");
            eval("\$deep_link_url = \"$inAppNotification->deep_link_url\";");

            //create inapp message against the user
            InAppMessage::create([
                'user_id'       => $user->id,
                'platform_id'   => $inAppNotification->platform_id,
                'content'       => $content,
                'description'   => $description,
                'deep_link_url' => $deep_link_url,
                'start_time'    => DateUtil::getDateTime(),
                'end_time'      => DateUtil::getFutureTime($inAppNotification->offset_end_time),
            ]);

        }
    }
}

