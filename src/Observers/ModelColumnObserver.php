<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class ModelColumnObserver extends BaseObserver {

    protected $rules = [
        'model_id' => 'required',
        'name'     => 'required',
    ];
}