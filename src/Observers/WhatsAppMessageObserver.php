<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class WhatsAppMessageObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class WhatsAppMessageObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'mobile'  => 'required',
        'content' => 'required',
    ];
}