<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class SystemScriptObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class SystemScriptObserver extends BaseObserver
{

    /**
     * @var array
     */
    protected $rules = [
        'source_type' => 'required',
        'source_id'   => 'required',
    ];
}
