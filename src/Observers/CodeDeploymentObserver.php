<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class CodeDeploymentObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class CodeDeploymentObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'private_ip' => 'required',
    ];
}
