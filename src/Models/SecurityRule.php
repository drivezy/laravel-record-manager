<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles () {
        return $this->hasMany(RoleAssignment::class, 'source_id')->where('source_type', self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function script () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new SecurityRuleObserver());
    }
}