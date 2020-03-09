<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Models\CodeCommit;
use Drivezy\LaravelRecordManager\Models\ServerDeployment;
use Drivezy\LaravelUtility\Job\BaseJob;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;
use Drivezy\LaravelUtility\Library\RemoteRequest;
use Illuminate\Support\Facades\Crypt;

/**
 * Class CodeCommitSyncJob
 * @package Drivezy\LaravelRecordManager\Jobs
 */
class CodeCommitSyncJob extends BaseJob
{
    /**
     * @return bool
     */
    public function handle ()
    {
        parent::handle();

        $commit = CodeCommit::find($this->id);
        $servers = $this->getActiveServers($commit);

        //if no servers are deployed against the deployment, then do nothing
        if ( !sizeof($servers) ) return false;

        $url = LaravelUtility::getProperty('deployment.server.url');
        $token = LaravelUtility::getProperty('deployment.server.access.key');

        //if properties are not setup do nothing
        if ( !( $url && $token ) ) return false;

        $token = Crypt::decrypt($token);

        //create the payload for the request
        $payload = [
            'id'                => $commit->id,
            'deployment_script' => 'custom-sync-all.sh',
            'branch'            => $commit->branch,
            'repository'        => $commit->repository_name,
            'instances'         => [],
        ];

        foreach ( $servers as $server ) {
            array_push($payload['instances'], [
                'instance_id' => $server->instance_identifier,
            ]);
        }

        RemoteRequest::pushJsonRequest($url . '/deploy', $payload, 'POST', ['access-key' => $token]);
    }

    /**
     * @param CodeCommit $commit
     * @return mixed
     */
    private function getActiveServers (CodeCommit $commit)
    {
        return ServerDeployment::where('repository_name', $commit->repository_name)
            ->where('branch', $commit->branch)
            ->where('last_ping_time', '>', DateUtil::getPastTime(2))
            ->whereNotNull('instance_identifier')
            ->get();
    }
}
