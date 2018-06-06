<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\SecurityRuleObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class SecurityRule
 * @package Drivezy\LaravelRecordManager\Models
 */
class SecurityRule extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_security_rules';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new SecurityRuleObserver());
    }
}