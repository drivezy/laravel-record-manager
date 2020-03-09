<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class ReadRecordController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class ReadRecordController extends BaseController
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request)
    {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update (Request $request, $id)
    {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy (Request $request, $id)
    {
        return Response::json(['success' => false, 'response' => 'invalid operation']);
    }
}
