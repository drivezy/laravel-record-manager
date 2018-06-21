<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\Column;

/**
 * Class ColumnController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class ColumnController extends RecordController {
    /**
     * @var string
     */
    protected $model = Column::class;
}