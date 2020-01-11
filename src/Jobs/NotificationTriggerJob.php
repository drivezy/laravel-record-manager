<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Library\Notification\NotificationManager;
use Drivezy\LaravelUtility\Job\BaseJob;

/**
 * Class NotificationTriggerJob
 * @package Drivezy\LaravelRecordManager\Jobs
 */
class NotificationTriggerJob extends BaseJob
{

    /**
     * @return bool|void
     */
    public function handle ()
    {
        parent::handle();

        //check if id is an array. if yes then first element is notification id
        //and second element is the data id
        if ( is_array($this->id) )
            ( new NotificationManager($this->id[0]) )->process($this->id[1]);
    }
}