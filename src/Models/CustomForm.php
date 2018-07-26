<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelAccessManager\Models\Route;
use Drivezy\LaravelRecordManager\Observers\CustomFormObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route () {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function method () {
        return $this->belongsTo(LookupValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form_type () {
        return $this->belongsTo(LookupValue::class);
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function columns () {
        return $this->hasMany(Column::class, 'source_id')->where('source_type', md5(self::class));
    }

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new CustomFormObserver());
    }
}