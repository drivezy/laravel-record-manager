<?php

namespace Drivezy\LaravelRecordManager\Controller;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;

/**
 * Class RoleAssignmentController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class RoleAssignmentController extends RecordController {
    /**
     * @var string
     */
    public $model = RoleAssignment::class;
}