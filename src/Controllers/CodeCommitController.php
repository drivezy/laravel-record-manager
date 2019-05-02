<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\CodeCommit;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Http\Request;

/**
 * Class CodeCommitController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class CodeCommitController extends RecordController {
    /**
     * @var string
     */
    protected $model = CodeCommit::class;

    /**
     * @param $key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logBitBucketCommit ($key, Request $request) {
        //validate key against the bitbucket
        if ( $key != LaravelUtility::getProperty('bitbucket.webhook.key') ) return invalid_operation();

        //just log the commit logs and not anything else
        if ( !isset($request->push) ) return success_response('Invalid request');

        $commitDetails = new CodeCommit();

        $commitDetails->repository_name = $request->repository['full_name'];
        $commitDetails->branch = $request->push['changes'][0]['new']['name'];

        $commitDetails->commit = $request->push['changes'][0]['new']['target']['hash'];
        $commitDetails->commit_url = $request->push['changes'][0]['new']['target']['links']['html']['href'];

        $commitDetails->commit_message = $request->push['changes'][0]['new']['target']['message'];
        $commitDetails->committed_by = $request->actor['username'];

        $commitDetails->save();

        return success_response($commitDetails);
    }
}
