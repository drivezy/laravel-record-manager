<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Library\AuditManager;
use Drivezy\LaravelRecordManager\Library\ObserverEvaluator;
use Drivezy\LaravelUtility\Job\BaseJob;

/**
 * Class ObserverEventManagerJob
 * @package Drivezy\LaravelRecordManager\Jobs
 */
class ObserverEventManagerJob extends BaseJob {
    public static $enabled = true;
    public $object;

    /**
     * ObserverEventManagerJob constructor.
     * @param $object
     */
    public function __construct ($object) {
        $this->object = serialize($object);
    }

    /**
     * @return bool|void
     * @throws \Exception
     */
    public function handle () {
        parent::handle();

        //validate if observer event processing is enabled or not
        if ( !self::$enabled ) return true;

        $model = unserialize($this->object);

        //record the audit log against the model activity
        ( new AuditManager($model) )->process();

        //find all activity registered against the given model activity
        ( new ObserverEvaluator($model) )->process();
    }
}
