<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Library\ColumnManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DataModelController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class DataModelController extends RecordController
{
    /**
     * @var string
     */
    public $model = DataModel::class;

    /**
     * get data related to the source polymorphic table
     * @param Request $request
     * @return JsonResponse
     */
    public function getSourceColumnDetails (Request $request)
    {
        return success_response(ColumnManager::getSourceColumnDetails($request->source_type, $request->source_id));
    }
}
