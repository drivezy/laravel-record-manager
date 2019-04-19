<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Illuminate\Http\Request;

/**
 * Class RoleAssignmentController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class RoleAssignmentController extends RecordController {
    /**
     * @var string
     */
    public $model = RoleAssignment::class;

    /**
     * Checks if role is already assigned. If not assigned assigns role.
     *
     * @see https://justride.atlassian.net/browse/DD-3998
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store (Request $request) {
        $record = $this->model::where('role_id', $request->get('role_id'))
            ->where('source_type', $request->get('source_type'))
            ->where('source_id', $request->get('source_id'))
            ->first();

        if ( $record ) return failed_response('Role already exists.');

        return parent::store($request);
    }
}