<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Library\AuditManager;
use Drivezy\LaravelRecordManager\Library\ObserverEvaluator;
use Drivezy\LaravelRecordManager\Models\ObserverEvent;
use Drivezy\LaravelUtility\Job\BaseJob;

/**
 * Class ObserverEventManagerJob
 * @package Drivezy\LaravelRecordManager\Jobs
 */
class ObserverEventManagerJob extends BaseJob {

    /**
     * ObserverEventManagerJob constructor.
     * @param $object
     * @param null $eventId
     */
    public function __construct ($object, $eventId = null) {
        parent::__construct(serialize($object), $eventId);
    }

    /**
     * @return bool|void
     * @throws \Exception
     */
    public function handle () {
        parent::handle();

        $object = unserialize($this->object);
        ( new AuditManager(unserialize($object->data)) )->process();

        $object->data_model = DataModel::find($object->model_id);
        ( new ObserverEvaluator($object) )->process();
    }
}
