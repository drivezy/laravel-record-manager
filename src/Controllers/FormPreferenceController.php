<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\FormPreference;
use Illuminate\Http\Request;

/**
 * Class FormPreferenceController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class FormPreferenceController extends ReadRecordController {
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

        return Response::json(['success' => true, 'response' => $preference]);
    }
}