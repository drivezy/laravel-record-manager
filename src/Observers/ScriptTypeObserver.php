<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ScriptTypeObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ScriptTypeObserver extends BaseObserver {

    /**
     * @var array
     */
    protected $rules = [
        'name' => 'required',
    ];
}