<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\WhatsAppTemplateObserver;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WhatsAppTemplate
 * @package Drivezy\LaravelRecordManager\Models
 */
class WhatsAppTemplate extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_whatsapp_templates';

    /**
     *
     */
    protected static function boot ()
    {
        parent::boot();
        self::observe(WhatsAppTemplateObserver::class);
    }

    /**
     * @return BelongsTo
     */
    public function gateway ()
    {
        return $this->belongsTo(LookupValue::class);
    }

}
