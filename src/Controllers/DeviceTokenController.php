<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\DeviceToken;

/**
 * Class DeviceTokenController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class DeviceTokenController extends RecordController {
    /**
     * @var string
     */
    protected $model = DeviceToken::class;
}
