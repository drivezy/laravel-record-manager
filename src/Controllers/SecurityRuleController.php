<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\SecurityRule;

/**
 * Class SecurityRuleController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class SecurityRuleController extends RecordController {
    /**
     * @var string
     */
    public $model = SecurityRule::class;
}