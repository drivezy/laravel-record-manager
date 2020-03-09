<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class ObserverActionObserver extends BaseObserver
{
    protected $rules = [
        'observer_rule_id' => 'required',
    ];
}
