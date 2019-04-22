<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\Models\UserGroupMember;
use Drivezy\LaravelRecordManager\Models\DeviceToken;
use Drivezy\LaravelRecordManager\Models\NotificationSubscriber;
use Drivezy\LaravelRecordManager\Models\NotificationTrigger;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;
use Illuminate\Support\Facades\DB;

class NotificationRecipientManager {
    protected $notification_data = null;
    protected $notification;
    protected $default_users;

    protected $fp;
    private $log_file;

    protected $trigger;

    protected $sms_count = 0;
    protected $email_count = 0;
    protected $push_count = 0;

    /**
     * NotificationRecipientManager constructor.
     */
    public function __construct () {
        $this->trigger = NotificationTrigger::create([
            'notification_id' => $this->notification->id,
            'start_time'      => DateUtil::getDateTime(),
        ]);
    }

    /**
     * @param $recipients
     * @return array
     */
    protected function getNotificationUsers ($recipients) {
        if ( !$recipients ) return [];
        $users = [];

        foreach ( $recipients as $recipient ) {
            if ( $this->validateRunCondition($recipient->run_condition) )
                $users = array_merge($users, self::prepareNotificationRecipient($recipient));
        }

        return self::getUniqueUserRecords($users);
    }

    /**
     * @param $default
     * @param $recipients
     * @return array
     */
    protected function getTotalUsers ($default, $recipients) {
        $users = !is_null($recipients) ? self::getNotificationUsers($recipients) : [];
        if ( $default )
            $users = self::getUniqueUserRecords(array_merge($users, $this->default_users));

        return $users;
    }

    /**
     * @param $recipient
     * @return array
     */
    private function prepareNotificationRecipient ($recipient) {
        if ( !$recipient ) return [];

        $users = [];

        $users = array_merge($users, self::getUsersFromUser($recipient));
        $users = array_merge($users, self::getUsersFromGroup($recipient));
        $users = array_merge($users, self::getUsersFromFields($recipient));
        $users = array_merge($users, self::getUsersFromQuery($recipient));
        $users = array_merge($users, self::getUserFromDirectInsertions($recipient));

        return $users;
    }

    /**
     * @param $recipient
     * @return array
     */
    private function getUsersFromUser ($recipient) {
        $users = [];
        if ( is_null($recipient->users) ) return $users;

        foreach ( $recipient->users as $user ) {
            array_push($users, self::getUserObjectFromUser($user));
        }

        return $users;
    }

    /**
     * get user record from the multiple group defined in the system
     * @param $recipient
     * @return array
     */
    private function getUsersFromGroup ($recipient) {
        $users = [];
        if ( is_null($recipient->user_groups) ) return $users;

        foreach ( $recipient->user_groups as $group ) {
            $users = array_merge($users, self::getGroupMemberUsers($group->id));
        }

        return $users;
    }

    /**
     * get user object from the query
     * @param $recipient
     * @return array
     */
    private function getUsersFromQuery ($recipient) {
        if ( !$recipient->query_id ) return [];
        $users = [];

        $data = $this->notification_data;
        if ( isset($recipient->custom_query) ) {
            $query = $recipient->custom_query->script;
            eval("\$query = \"$query\";");

            if ( $query ) {
                try {
                    $rows = DB::select(DB::raw($query));
                    foreach ( $rows as $row ) {
                        array_push($users, self::getUserObjectFromUser($row));
                    }
                } catch ( Exception $e ) {
                }
            }
        }

        return $users;
    }

    /**
     * Get user object from the column in the object model
     * @param $recipient
     * @return array
     */
    private function getUsersFromFields ($recipient) {
        if ( !$recipient->fields ) return [];
        $users = [];

        foreach ( $recipient->fields as $column ) {
            $columnValue = $this->notification_data[ $column->column_name ];
            if ( !$columnValue ) continue;

            if ( $column->referenced_model_id == 80 )
                array_push($users, self::getUserObject($columnValue));

            if ( $column->referenced_model_id == 81 )
                $users = array_merge($users, self::getGroupMemberUsers($columnValue));
        }

        return $users;
    }

    /**
     * get users from direct user insertions
     * @param $recipient
     * @return array
     */
    protected function getUserFromDirectInsertions ($recipient) {
        if ( !$recipient->direct_users ) return [];

        $users = [];
        foreach ( $recipient->direct_users as $user )
            array_push($users, (object) $user);

        return $users;
    }

    /**
     * create user object from user id
     * @param $id
     * @return object
     */
    private function getUserObject ($id) {
        $userClass = LaravelUtility::getUserModelFullQualifiedName();
        $user = $userClass::find($id);
        $obj = self::createRawTemplateForUser();

        $subscription = NotificationSubscriber::where('notification_id', $this->notification->id)->where('source_type', md5($userClass))->first();
        if ( $subscription ) {
            $obj->email = $subscription->email ? $user->email : null;
            $obj->mobile = $subscription->sms ? $user->mobile : null;
            $obj->id = $subscription->push ? $id : null;
            $obj->name = 'Rider';
        } else {
            $obj->email = $user->email;
            $obj->mobile = $user->mobile;
            $obj->id = $id;
            $obj->name = 'Rider';
        }

        return $obj;
    }

    /**
     * create user object against a user model object
     * @param $user
     * @return object
     */
    private function getUserObjectFromUser ($user) {
        $obj = self::createRawTemplateForUser();

        if ( isset($user->id) )
            $subscription = NotificationSubscriber::where('notification_id', $this->notification->id)->where('user_id', $user->id)->first();

        if ( $user && isset($subscription) ) {
            $obj->email = $subscription->email ? $user->email : null;
            $obj->mobile = $subscription->sms ? $user->mobile : null;
            $obj->name = 'Rider';
        } else {
            $obj->email = isset($user->email) ? $user->email : null;
            $obj->mobile = isset($user->mobile) ? $user->mobile : null;
            $obj->id = isset($user->id) ? $user->id : null;
            $obj->name = 'Rider';
        }

        return $obj;
    }

    /**
     * get group members against a group
     * @param $groupId
     * @return array
     */
    private function getGroupMemberUsers ($groupId) {
        $users = [];
        $members = UserGroupMember::with('user')->where('group_id', $groupId)->get();
        foreach ( $members as $member ) {
            $obj = self::createRawTemplateForUser();

            $obj->email = $member->user->email;
            $obj->mobile = $member->user->mobile;
            $obj->id = $member->user->id;
            $obj->name = 'Rider';

            array_push($users, $obj);
        }

        return $users;
    }

    /**
     * @return object
     */
    private function createRawTemplateForUser () {
        return (object) [
            'email'  => null,
            'mobile' => null,
            'id'     => null,
            'name'   => null,
        ];
    }

    /**
     * get unique user record
     * @param $users
     * @return array
     */
    private function getUniqueUserRecords ($users) {
        $processedIds = [];
        $uniqueUsers = [];

        foreach ( $users as $user ) {
            if ( isset($user->id) ) {
                if ( in_array($user->id, $processedIds) )
                    continue;

                array_push($processedIds, $user->id);
            }
            array_push($uniqueUsers, $user);
        }

        return $uniqueUsers;
    }

    /**
     * Get registered device against push notification
     * @param $users
     * @param $pushNotification
     * @return mixed
     */
    protected function getPushNotificationDevices ($users, $pushNotification) {
        $data = $this->notification_data;

        $arr = [];
        foreach ( $users as $user ) {
            if ( isset($user->id) ) {
                if ( !$this->validateSubscription($user, 'push') ) continue;
                array_push($arr, $user->id);
            }
        }

        $devices = DeviceToken::whereIn('user_id', $arr);
        if ( $pushNotification->target_devices ) {
            $targetDevices = [];
            foreach ( $pushNotification->target_devices as $device )
                array_push($targetDevices, $device->id);

            $devices = $devices->whereIn('source', $targetDevices);
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

    /**
     * This would check if the given condition is correct or not
     * @param string $condition
     * @param object $data
     * @return bool
     */
    protected function validateRunCondition ($condition, $data = null) {
        if ( !( $condition && $condition->script ) ) return true;

        $answer = false;
        $data = $data ? : $this->notification_data;

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

    /**
     * This would sum up the entire notification triggers
     */
    public function __destruct () {
        $this->trigger->sms_notifications = $this->sms_count;
        $this->trigger->push_notifications = $this->push_count;
        $this->trigger->email_notifications = $this->email_count;

        $this->trigger->log_file = 'test';
        $this->trigger->end_time = DateUtil::getDateTime();
        $this->trigger->save();
    }
}
