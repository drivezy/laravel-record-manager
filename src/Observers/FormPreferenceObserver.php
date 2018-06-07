<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class FormPreferenceObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class FormPreferenceObserver extends BaseObserver {

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
        if ( !$isFormConfigurator ) return false;

        return parent::updating($model);
    }

    /**
     * @param Eloquent $model
     * @return bool|void
     */
    public function deleting (Eloquent $model) {
        $isFormConfigurator = AccessManager::hasPermission('form-configurator');
        if ( !$isFormConfigurator ) return false;

        parent::deleting($model);
    }


}