<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class InAppNotificationObserver
 */
class InAppNotificationObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'user_id' => 'required',
        'content' => 'required',
    ];
}
