<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\PermissionAssignment;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Observers\CustomFormObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class CustomForm
 * @package Drivezy\LaravelRecordManager\Models
 */
class CustomForm extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_custom_forms';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function columns () {
        return $this->hasMany(Column::class, 'source_id')->where('source_type', self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles () {
        return $this->hasMany(RoleAssignment::class, 'source_id')->where('source_type', self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions () {
        return $this->hasMany(PermissionAssignment::class, 'source_id')->where('source_type', self::class);
    }


    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new CustomFormObserver());
    }
}