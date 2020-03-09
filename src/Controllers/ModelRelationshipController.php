<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\ModelRelationship;

/**
 * Class ModelRelationshipController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class ModelRelationshipController extends RecordController
{
    /**
     * @var string
     */
    public $model = ModelRelationship::class;
}
