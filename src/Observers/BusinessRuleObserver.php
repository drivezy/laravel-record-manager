<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class BusinessRuleObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class BusinessRuleObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'model_id' => 'required',
    ];
}