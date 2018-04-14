<?php

namespace Drivezy\LaravelRecordManager\Controller;

use Drivezy\LaravelAccessManager\Models\Permission;

/**
 * Class PermissionController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class PermissionController extends RecordController {
    /**
     * @var string
     */
    public $model = Permission::class;
}