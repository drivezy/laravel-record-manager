<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\DocumentManager;

/**
 * Class DocumentController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class DocumentController extends RecordController
{
    /**
     * @var string
     */
    public $model = DocumentManager::class;
}
