<?php

namespace Drivezy\LaravelRecordManager\Controller;

use Drivezy\LaravelAccessManager\Models\UserGroupMember;

/**
 * Class UserGroupMemberController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class UserGroupMemberController extends RecordController {
    /**
     * @var string
     */
    public $model = UserGroupMember::class;
}