<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObserverEvent extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_observer_events';

    public function setDataAttribute ($obj)
    {
        $this->attributes['data'] = serialize($obj);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive ($query)
    {
        return $query->whereNull('processed_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending ($query)
    {
        return $query->whereNull('processed_at');
    }

    /**
     * @return BelongsTo
     */
    public function data_model ()
    {
        return $this->belongsTo(DataModel::class, 'model_hash', 'model_hash');
    }
}
