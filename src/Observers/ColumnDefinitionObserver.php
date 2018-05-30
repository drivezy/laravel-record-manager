<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class ColumnDefinitionObserver extends BaseObserver {

    protected $rules = [
        'name'        => 'required',
        'description' => 'required',
    ];

}