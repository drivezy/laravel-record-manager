<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ColumnObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ColumnObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'source_type' => 'required',
        'source_id'   => 'required',
    ];
}
