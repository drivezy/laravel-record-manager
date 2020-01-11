<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\WhatsAppMessageObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class WhatsAppMessage
 * @package Drivezy\LaravelRecordManager\Models
 */
class WhatsAppMessage extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_whatsapp_messages';

    /**
     * Override the boot functionality to add up the observer
     */
    public static function boot ()
    {
        parent::boot();
        self::observe(new WhatsAppMessageObserver());
    }

}