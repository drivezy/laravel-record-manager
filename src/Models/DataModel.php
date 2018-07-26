<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelAdmin\Models\ClientScript;
use Drivezy\LaravelAdmin\Models\UIAction;
use Drivezy\LaravelRecordManager\Observers\ModelObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class Model
 * @package Drivezy\LaravelRecordManager\Models
 */
class DataModel extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_model_details';
    /**
     * @var array
     */
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function columns () {
        return $this->hasMany(Column::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relationships () {
        return $this->hasMany(ModelRelationship::class, 'model_id');
    }

    /**
     * @return $this
     */
    public function roles () {
        return $this->hasMany(RoleAssignment::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ui_actions () {
        return $this->hasMany(UIAction::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function client_scripts () {
        return $this->hasMany(ClientScript::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function security_rules () {
        return $this->hasMany(SecurityRule::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelObserver());
    }
}