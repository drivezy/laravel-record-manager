<?php

namespace Drivezy\LaravelRecordManager\Models;

use Webpatser\Uuid\Uuid;

/**
 * Trait UsesUuid
 * @package JRApp\Models
 */
trait UsesUuid {

    /**
     *
     */
    protected static function bootUsesUuid () {
        static::creating(function ($model) {
            $model->uuid = (string) Uuid::generate();
        });
    }
}
