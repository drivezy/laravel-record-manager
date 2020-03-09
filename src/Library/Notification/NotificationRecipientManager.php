<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Models\NotificationTrigger;
use Drivezy\LaravelUtility\Library\DateUtil;

class NotificationRecipientManager
{
    protected $notification_data = null;
    protected $notification;
    protected $default_users;

    protected $fp;
    protected $trigger;
    protected $sms_count = 0;
    protected $email_count = 0;
    protected $push_count = 0;
    private $log_file;

    /**
     * NotificationRecipientManager constructor.
     */
    public function __construct ()
    {
        $this->trigger = NotificationTrigger::create([
            'notification_id' => $this->notification->id,
            'start_time'      => DateUtil::getDateTime(),
            'log_file'        => 'ongoing',
        ]);
    }

    /**
     * This would sum up the entire notification triggers
     */
    public function __destruct ()
    {
        $this->trigger->sms_notifications = $this->sms_count;
        $this->trigger->push_notifications = $this->push_count;
        $this->trigger->email_notifications = $this->email_count;

        $this->trigger->log_file = 'test';
        $this->trigger->end_time = DateUtil::getDateTime();
        $this->trigger->save();
    }

    /**
     * This would check if the given condition is correct or not
     * @param string $condition
     * @param object $data
     * @return bool
     */
    protected function validateRunCondition ($condition, $data = null)
    {
        if ( !( $condition && $condition->script ) ) return true;

        $answer = false;
        $data = $data ? : $this->notification_data;

        eval($condition->script);

        return $answer;
    }
}
