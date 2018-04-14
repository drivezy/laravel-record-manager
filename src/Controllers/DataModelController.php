<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\DataModel;

/**
 * Class DataModelController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class DataModelController extends RecordController {
    /**
     * @var string
     */
    public $model = DataModel::class;
}