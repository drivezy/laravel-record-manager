<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\SystemScript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class SystemScriptController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class SystemScriptController extends RecordController {
    /**
     * @var string
     */
    public $model = SystemScript::class;

    /**
     * reattach the data to the existing model
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store (Request $request) {
        $object = SystemScript::firstOrNew([
            'source_type'   => $request->source_type,
            'source_id'     => $request->source_id,
            'source_column' => $request->has('source_column') ? $request->source_column : null,
        ]);

        $object->name = $request->name;
        $object->description = $request->description;

        $object->save();

        return Response::json(['success' => true, 'response' => $object]);
    }
}