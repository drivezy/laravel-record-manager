<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelAccessManager\Models\UserGroup;
use Drivezy\LaravelAccessManager\Models\UserGroupMember;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class NotificationUserManager
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class  NotificationUserManager
{
    private $user_name = null;

    /**
     * NotificationUserManager constructor.
     * @param array $args
     */
    public function __construct ($args = [])
    {
        foreach ( $args as $key => $value )
            $this->{$key} = $value;

        $this->user_name = LaravelUtility::getProperty('notification.user.display', false);
    }

    /**
     * @param $recipients
     * @return array
     */
    public function getNotificationUsers ($recipients)
    {
        if ( !$recipients ) return [];
        $users = [];

        foreach ( $recipients as $recipient ) {
            if ( BaseNotification::validateCondition($recipient->run_condition, $this->data) )
                $users = array_merge($users, self::prepareNotificationRecipient($recipient));
        }

        return self::getUniqueUserRecords($users);
    }

    /**
     * @param $default
     * @param $recipients
     * @return array
     */
    public function getTotalUsers ($default, $recipients)
    {
        $users = !is_null($recipients) ? self::getNotificationUsers($recipients) : [];
        if ( $default )
            $users = self::getUniqueUserRecords(array_merge($users, $this->default_users));

        return $users;
    }

    /**
     * @param $recipient
     * @return array
     */
    private function prepareNotificationRecipient ($recipient)
    {
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
    private function getUsersFromUser ($recipient)
    {
        $users = [];
        if ( is_null($recipient->users) ) return $users;

        foreach ( $recipient->users as $user ) {
            array_push($users, self::createRawTemplateForUser($user));
        }

        return $users;
    }

    /**
     * get user record from the multiple group defined in the system
     * @param $recipient
     * @return array
     */
    private function getUsersFromGroup ($recipient)
    {
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
    private function getUsersFromQuery ($recipient)
    {
        if ( !$recipient->query_id ) return [];
        $users = [];

        $data = $this->data;
        if ( isset($recipient->custom_query) ) {
            $query = $recipient->custom_query->script;
            eval("\$query = \"$query\";");

            if ( $query ) {
                try {
                    $rows = DB::select(DB::raw($query));
                    foreach ( $rows as $row ) {
                        array_push($users, self::createRawTemplateForUser($row));
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
    private function getUsersFromFields ($recipient)
    {
        if ( !$recipient->fields ) return [];
        $users = [];

        foreach ( $recipient->fields as $column ) {
            $columnValue = $this->data[ $column->name ];
            if ( !$columnValue ) continue;

            if ( $column->reference_model_id == $this->getUserClassModelId() )
                array_push($users, self::getUserObject($columnValue));

            if ( $column->reference_model_id == $this->getUserGroupClassModelId() )
                $users = array_merge($users, self::getGroupMemberUsers($columnValue));
        }

        return $users;
    }

    /**
     * get users from direct user insertions
     * @param $recipient
     * @return array
     */
    public function getUserFromDirectInsertions ($recipient)
    {
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
    private function getUserObject ($id)
    {
        $userClass = LaravelUtility::getUserModelFullQualifiedName();
        $user = $userClass::find($id);

        return self::createRawTemplateForUser($user);
    }

    /**
     * get group members against a group
     * @param $groupId
     * @return array
     */
    private function getGroupMemberUsers ($groupId)
    {
        $users = [];
        $members = UserGroupMember::with('user')->where('group_id', $groupId)->get();
        foreach ( $members as $member ) {
            array_push($users, self::createRawTemplateForUser($member->user));
        }

        return $users;
    }

    /**
     * @param null $user
     * @return object
     */
    private function createRawTemplateForUser ($user = null)
    {
        return (object) [
            'email'  => $user ? ( $user->email ?? null ) : null,
            'mobile' => $user ? ( $user->mobile ?? null ) : null,
            'id'     => $user ? ( $user->id ?? null ) : null,
            'name'   => $user ? $this->getUserDisplayName($user) : null,
        ];
    }

    /**
     * get unique user record
     * @param $users
     * @return array
     */
    private function getUniqueUserRecords ($users)
    {
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
     * @return mixed
     */
    private function getUserClassModelId ()
    {
        $identifier = 'dz_notification_details.user.class.id';
        $id = Cache::get($identifier, false);
        if ( !$id ) {
            $record = DataModel::where('model_hash', md5(LaravelUtility::getUserModelFullQualifiedName()))->first();
            if ( $record ) {
                Cache::forever($identifier, $record->id);
                $id = $record->id;
            }
        }

        return $id;
    }

    /**
     * @return mixed
     */
    private function getUserGroupClassModelId ()
    {
        $identifier = 'dz_notification_details.user.group.class.id';
        $id = Cache::get($identifier, false);
        if ( !$id ) {
            $record = DataModel::where('model_hash', md5(UserGroup::class))->first();
            if ( $record ) {
                Cache::forever($identifier, $record->id);
                $id = $record->id;
            }
        }

        return $id;
    }

    /**
     * @param $user
     * @return string
     */
    private function getUserDisplayName ($user = null)
    {
        $name = 'Rider';

        if ( $this->user_name && !isset($user->name) )
            eval($this->user_name);

        return $name;
    }
}
