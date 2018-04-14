<?php

namespace Drivezy\LaravelRecordManager\Controller;

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