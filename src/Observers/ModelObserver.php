<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class ModelObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ModelObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'name'       => 'required',
        'namespace'  => 'required',
        'model_hash' => 'required',
    ];

    /**
     * @param Eloquent $model
     * @return bool
     */
    public function saving (Eloquent $model)
    {
        $model->model_hash = md5($model->namespace . '\\' . $model->name);

        return parent::saving($model);
    }
}
