<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

/**
 * Class ListPreferenceObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ListPreferenceObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param Eloquent $model
     * @return bool
     */
    public function saving (Eloquent $model) {
        $isFormConfigurator = AccessManager::hasPermission('form-configurator');

        if ( !$isFormConfigurator && $model->user_id != Auth::id() )
            return false;

        return parent::saving($model);
    }

    /**
     * @param Eloquent $model
     * @return bool|void
     */
    public function deleting (Eloquent $model) {
        $isFormConfigurator = AccessManager::hasPermission('form-configurator');

        if ( !$isFormConfigurator && $model->user_id != Auth::id() )
            return false;

        parent::deleting($model);
    }
}