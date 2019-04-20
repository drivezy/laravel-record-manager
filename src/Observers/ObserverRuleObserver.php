<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class ObserverRuleObserver extends BaseObserver {
    protected $rules = [
        'name'     => 'required',
        'model_id' => 'required',
    ];
}
