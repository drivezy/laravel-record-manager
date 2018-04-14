<?php

namespace Drivezy\LaravelRecordManager\Controller;

use Drivezy\LaravelAccessManager\Models\UserGroup;

/**
 * Class UserGroupController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class UserGroupController extends RecordController {
    /**
     * @var string
     */
    public $model = UserGroup::class;
}