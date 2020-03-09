<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\Models\PermissionAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PermissionAssignmentController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class PermissionAssignmentController extends RecordController
{
    /**
     * @var string
     */
    public $model = PermissionAssignment::class;

    /**
     * Checks if permission is already assigned. If not assigned assigns permission.
     *
     * @see https://justride.atlassian.net/browse/DD-3998
     *
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function store (Request $request)
    {
        $record = $this->model::where('permission_id', $request->get('permission_id'))
            ->where('source_type', $request->get('source_type'))
            ->where('source_id', $request->get('source_id'))
            ->first();

        if ( $record ) return failed_response('Permission already exists.');

        return parent::store($request);
    }
}
