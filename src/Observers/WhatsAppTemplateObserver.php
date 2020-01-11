<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class WhatsAppTemplateObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class WhatsAppTemplateObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'name'       => 'required',
        'identifier' => 'required',
        'content'    => 'required',
    ];
}