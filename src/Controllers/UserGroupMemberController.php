<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\Models\UserGroupMember;
use Illuminate\Http\Request;

/**
 * Class UserGroupMemberController
 * @package Drivezy\LaravelRecordManager\Controller
 */
class UserGroupMemberController extends RecordController {
    /**
     * @var string
     */
    public $model = UserGroupMember::class;
    
    /**
     * Checks if user already in group. If not assigns user to group.
     *
     * @see https://justride.atlassian.net/browse/DD-3998
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store (Request $request) {
        $user = $this->model::where('user_group_id', $request->get('user_group_id'))
            ->where('user_id', $request->get('user_id'))
            ->first();

        if ( $user ) return failed_response('User already in user group.');

        return parent::store($request);
    }
}