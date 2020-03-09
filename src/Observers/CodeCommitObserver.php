<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelRecordManager\Jobs\CodeCommitSyncJob;
use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CodeCommitObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class CodeCommitObserver extends BaseObserver
{
    protected $rules = [
        'repository_name' => 'required',
        'branch'          => 'required',
    ];

    public function created (Eloquent $model)
    {
        parent::created($model);

        //run job to handle syncing of code across all servers
        dispatch(new CodeCommitSyncJob($model->id));
    }
}
