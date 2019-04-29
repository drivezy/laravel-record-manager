<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ServerDeploymentObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ServerDeploymentObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'hostname' => 'required',
    ];
}
