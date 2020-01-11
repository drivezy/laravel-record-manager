<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\NotificationSubscriber;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class BaseNotification
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class BaseNotification {
    protected $data = null;
    protected $notification = null;
    protected $default_users = null;

    protected $user_request_object = [];

    /**
     * BaseNotification constructor.
     * @param array $args
     */
    public function __construct ($args = []) {
        //assign the input params as pushed
        foreach ( $args as $key => $value )
            $this->{$key} = $value;

        //create the frequent request object as required
        // user manager to process the base data
        $this->user_request_object = [
            'notification'  => $this->notification,
            'data'          => $this->data,
            'default_users' => $this->default_users,
        ];
    }

    /**
     * @param $condition
     * @param null $data
     * @return bool
     */
    protected function validateRunCondition($condition, $data = null){
        $data = $data ? : $this->data;
        return self::validateCondition($condition, $data);
    }

    /**
     * @param $condition
     * @param null $data
     * @return bool
     */
    public static function validateCondition ($condition, $data = null) {
        if ( !( $condition && $condition->script ) ) return true;

        $answer = false;

        eval($condition->script);

        return $answer;
    }

    /**
     * validate if the user has un-subscribed against the given subscription
     * @param $user
     * @param $type
     * @return bool
     */
    protected function validateSubscription ($user, $type) {
        if ( !isset($user->id) ) return true;

        $count = NotificationSubscriber::where('notification_id', $this->notification->id)
            ->where('source_type', md5(LaravelUtility::getUserModelFullQualifiedName()))
            ->where('source_id', $user->id)
            ->where($type, false)
            ->count();

        return $count ? false : true;
    }
}

