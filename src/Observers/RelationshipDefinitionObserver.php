<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class RelationshipDefinitionObserver extends BaseObserver {

    protected $rules = [
        'name'        => 'required',
        'description' => 'required',
    ];
    
}