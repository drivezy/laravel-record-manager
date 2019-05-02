<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\CodeDeployment;

/**
 * Class CodeDeploymentController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class CodeDeploymentController extends RecordController {
    /**
     * @var string
     */
    protected $model = CodeDeployment::class;
}
