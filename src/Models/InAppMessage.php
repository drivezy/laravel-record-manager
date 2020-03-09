<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\InAppMessageObserver;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InAppMessage
 * @package Drivezy\LaravelRecordManager\Models
 */
class InAppMessage extends BaseModel
{
    use UsesUuid;
    /**
     * @var string
     */
    protected $table = 'dz_inapp_messages';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new InAppMessageObserver());
    }

    /**
     * @return BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo(LaravelUtility::getUserModelFullQualifiedName());
    }

    /**
     * @return BelongsTo
     */
    public function platform ()
    {
        return $this->belongsTo(LookupValue::class);
    }
}

