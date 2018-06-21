<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ClientScriptObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ClientScriptObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'source_type' => 'required',
        'source_id'   => 'required',
    ];
}