<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\CodeDeploymentObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class CodeDeployment
 * @package Drivezy\LaravelRecordManager\Models
 */
class CodeDeployment extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_code_deployments';

    /**
     * Over ride the default observer
     */
    protected static function boot () {
        parent::boot();
        self::observe(new CodeDeploymentObserver());
    }
}
