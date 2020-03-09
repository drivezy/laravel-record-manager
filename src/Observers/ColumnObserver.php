<?php

namespace Drivezy\LaravelRecordManager\Observers;

use Drivezy\LaravelRecordManager\Library\ModelManager;
use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Cache;

/**
 * Class ColumnObserver
 * @package Drivezy\LaravelRecordManager\Observers
 */
class ColumnObserver extends BaseObserver
{
    /**
     * @var array
     */
    protected $rules = [
        'source_type' => 'required',
        'source_id'   => 'required',
    ];

    /**
     * @param Eloquent $model
     * @return bool
     */
    public function saving (Eloquent $model)
    {
        //if a column is defined as dynamo,
        // then it should always be the custom column
        if ( $model->column_type_id == 25 )
            $model->is_custom_column = true;

        return parent::saving($model);
    }

    /**
     * @param Eloquent $model
     */
    public function saved (Eloquent $model)
    {
        parent::saved($model);

        //set cache for the audit double column
        if ( $model->hasChanged('is_double_audit_enabled') || $model->hasChanged('column_type_id') ) {
            $modelHash = DataModel::find($model->source_id)->model_hash;

            ModelManager::getDoubleAuditColumns($modelHash);
            ModelManager::getDynamoColumns($modelHash, true);
        }
    }

    public function created (Eloquent $model)
    {
        parent::created($model);
        if ( $model->column_type_id == 25 ) {
            $modelHash = DataModel::find($model->source_id)->model_hash;
            ModelManager::getDynamoColumns($modelHash, true);
        }
    }
}
