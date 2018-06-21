<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\FormPreference;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class FormPreferenceController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class FormPreferenceController extends RecordController {
    /**
     * @var string
     */
    public $model = FormPreference::class;

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request) {
        $preference = FormPreference::firstOrNew([
            'source_type' => $request->source_type,
            'source_id'   => $request->source_id,
            'name'        => $request->name,
            'identifier'  => $request->identifier,
        ]);

        $preference->column_definition = $request->column_definition;
        $preference->save();

        if ( !isset($preference->errors) )
            return Response::json(['success' => true, 'response' => $preference]);

        return Response::json(['success' => false, 'response' => $preference, 'reason' => 'Validation error']);
    }
}