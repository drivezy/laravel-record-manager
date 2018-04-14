<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\Models\PermissionAssignment;

/**
 * Class PermissionAssignmentController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class PermissionAssignmentController extends RecordController {
    /**
     * @var string
     */
    public $model = PermissionAssignment::class;
}