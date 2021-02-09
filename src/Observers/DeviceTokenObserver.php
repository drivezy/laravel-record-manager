<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelUtility\Library\DateUtil;
use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

/**
 * Class DeviceTokenObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class DeviceTokenObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param Eloquent $model
     * @return bool
     */
    public function saving (Eloquent $model)
    {
        //set user if they are logged in
        if ( Auth::check() )
            $model->user_id = Auth::user()->id;

        $model->last_access_time = DateUtil::getDateTime();

        return parent::saving($model); // TODO: Change the autogenerated stub
    }
}
