<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ModelColumnObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ModelColumnObserver extends BaseObserver {

    /**
     * @var array
     */
    protected $rules = [
        'model_id' => 'required',
        'name'     => 'required',
    ];
}