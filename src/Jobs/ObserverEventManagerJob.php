<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Library\AuditManager;
use Drivezy\LaravelRecordManager\Library\ObserverEvaluator;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ObserverEvent;
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

        $object = unserialize($this->object);
        ( new AuditManager(unserialize($object->data)) )->process();

        $object->data_model = DataModel::find($object->model_id);
        ( new ObserverEvaluator($object) )->process();
    }
}
