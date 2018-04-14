<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelUtility\Models\Property;

/**
 * Class PropertyController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class PropertyController extends RecordController {
    /**
     * @var string
     */
    public $model = Property::class;
}