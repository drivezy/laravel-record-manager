<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\ServerDeploymentObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ServerDeployment
 * @package Drivezy\LaravelRecordManager\Models
 */
class ServerDeployment extends BaseModel
{
    /**
     * @var bool
     */
    public $auditable = false;
    /**
     * @var string
     */
    protected $table = 'dz_server_deployments';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new ServerDeploymentObserver());
    }
}
