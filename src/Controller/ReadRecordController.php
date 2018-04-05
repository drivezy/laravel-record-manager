<?php

namespace Drivezy\LaravelRecordManager\Controller;

use Illuminate\Http\Request;

/**
 * Class ReadRecordController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class ReadRecordController extends RecordManager {

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request) {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update (Request $request, $id) {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy ($id) {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }
}