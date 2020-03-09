<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class SMSMessageObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class SMSMessageObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'mobile'  => 'required',
        'content' => 'required',
    ];
}
