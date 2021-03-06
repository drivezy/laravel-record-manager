<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\DeviceToken;
use Illuminate\Http\Request;

/**
 * Class DeviceTokenController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class DeviceTokenController extends RecordController
{
    /**
     * @var string
     */
    protected $model = DeviceToken::class;

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function store (Request $request)
    {
        $token = DeviceToken::where('token', $request->token)->first();
        if ( $token )
            return parent::update($request, $token->id);

        return parent::store($request); // TODO: Change the autogenerated stub
    }
}
