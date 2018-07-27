<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Observers\BusinessRuleObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

/**
 * Class BusinessRule
 * @package Drivezy\LaravelRecordManager\Models
 */
class BusinessRule extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_business_rules';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function script () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function execution_type () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filter_condition () {
        return $this->belongsTo(SystemScript::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model () {
        return $this->belongsTo(DataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles () {
        return $this->hasMany(RoleAssignment::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     *
     */
    public static function boot () {
        parent::boot();
        self::observe(new BusinessRuleObserver());
    }
}