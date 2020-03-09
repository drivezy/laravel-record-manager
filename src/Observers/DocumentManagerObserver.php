<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

class DocumentManagerObserver extends BaseObserver
{
    protected $rules = [
        'source_type'  => 'required',
        'source_id'    => 'required|numeric',
        'document_url' => 'required',
    ];
}
