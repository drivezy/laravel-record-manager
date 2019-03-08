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

    /**
     * ObserverEventManagerJob constructor.
     * @param $id
     * @param null $eventId
     */
    public function __construct ($id, $eventId = null) {
        parent::__construct($id, $eventId);
    }

    /**
     * @return bool
     */
    public function handle () {
        return parent::handle();

        $event = ObserverEvent::with('data_model')->find($this->id);

        ( new AuditManager(unserialize($event->data)) )->setAuditRecord();
        ( new ObserverEvaluator($event) )->process();

    }
}
