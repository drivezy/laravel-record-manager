<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class SecurityRuleObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class SecurityRuleObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'source_type' => 'required',
        'source_id'   => 'required',
    ];
}
