<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class CodeCommitObserver extends BaseObserver {
    protected $rules = [
        'repository_name' => 'required',
        'branch' => 'required',
    ];
}
