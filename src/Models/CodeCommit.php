<?php

namespace Drivezy\LaravelRecordManager\Models;

use Drivezy\LaravelRecordManager\Observers\CodeCommitObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class CodeCommit
 * @package Drivezy\LaravelRecordManager\Models
 */
class CodeCommit extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'dz_code_commits';

    /**
     * Over ride the boot functionality
     */
    protected static function boot ()
    {
        parent::boot();
        self::observe(new CodeCommitObserver());
    }

}
