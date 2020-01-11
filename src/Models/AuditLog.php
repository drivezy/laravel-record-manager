<?php

namespace Drivezy\LaravelRecordManager\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AuditLog
 * @package Drivezy\LaravelRecordManager\Models
 */
class AuditLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'dz_audit_logs';
    /**
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];
}