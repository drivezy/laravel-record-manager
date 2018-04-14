<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\Models\Role;

/**
 * Class RoleController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class RoleController extends RecordController {
    /**
     * @var string
     */
    public $model = Role::class;
}