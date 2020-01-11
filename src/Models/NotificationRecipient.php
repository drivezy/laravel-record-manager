<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\UserGroup;
use Drivezy\LaravelRecordManager\Observers\NotificationRecipientObserver;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class NotificationRecipient
 * @package Drivezy\LaravelRecordManager\Models
 */
class NotificationRecipient extends BaseModel {

    /**
     * @var string
     */
    protected $table = 'dz_notification_recipients';

    /**
     * @param $str
     * @return |null
     */
    public function getUsersAttribute ($str) {
        if ( !$str ) return null;

        $userClass = LaravelUtility::getUserModelFullQualifiedName();

        return $userClass::whereIn('id', explode(',', $str))->get();
    }

    /**
     * @param $str
     * @return |null
     */
    public function getUserGroupsAttribute ($str) {
        if ( !$str ) return null;

        return UserGroup::whereIn('id', explode(',', $str))->get();
    }

    /**
     * @param $str
     * @return |null
     */
    public function getFieldsAttribute ($str) {
        if ( !$str ) return null;
        

        return Column::whereIn('id', explode(',', $str))->get();
    }

    /**
     * @param $arr
     */
    public function setDirectUsersAttribute ($arr) {
        $this->attributes['direct_users'] = serialize($arr);
    }

    /**
     * @param $obj
     * @return mixed
     */
    public function getDirectUsersAttribute ($obj) {
        return unserialize($obj);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custom_query () {
        return $this->belongsTo(SystemScript::class, 'query_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function run_condition () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new NotificationRecipientObserver());
    }
}
