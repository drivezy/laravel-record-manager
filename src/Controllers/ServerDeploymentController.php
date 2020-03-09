<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\ServerDeployment;
use Drivezy\LaravelUtility\Library\DateUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ServerDeploymentController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class ServerDeploymentController extends RecordController
{
    /**
     * @var string
     */
    protected $model = ServerDeployment::class;

    /**
     * Over riding the store method so as to avoid duplicate record
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function store (Request $request)
    {
        $deployment = ServerDeployment::firstOrNew($request->only(['private_ip', 'repository_name', 'branch']));

        if ( !$deployment->repository_name ) return failed_response('invalid params');

        $deployment->hostname = $request->hostname;
        $deployment->last_ping_time = DateUtil::getDateTime();
        $deployment->commit = $request->commit;
        $deployment->active = true;

        $deployment->save();

        return success_response($deployment);
    }
}
