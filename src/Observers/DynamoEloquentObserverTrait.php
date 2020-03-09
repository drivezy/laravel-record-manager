<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Trait DynamoEloquentObserverTrait
 * @package Drivezy\LaravelRecordManager\Observers
 */
trait DynamoEloquentObserverTrait
{
    /**
     * Remove all dynamo elements against the given model so
     * that they are not saved within the normal transactional db
     * @param Eloquent $model
     * @return Eloquent
     */
    public function preSaving (Eloquent $model)
    {
        if ( !$this->validateIfRequired($model) ) return $model;

        $model->setDynamoColumns();

    }

    /**
     * check if dynamo eloquent trait is available or not
     * @param Eloquent $model
     * @return bool
     */
    private function validateIfRequired (Eloquent $model)
    {
        if ( isset($model->dynamo_columns) ) return true;

        return false;
    }

    /**
     * Save the elements saved for dynamo columns to the dynamodb
     * @param Eloquent $model
     * @return Eloquent
     */
    public function postSaved (Eloquent $model)
    {
        if ( !$this->validateIfRequired($model) ) return $model;

        $model->setDynamoAttributes();
    }
}
