<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ObserverRule;

/**
 * Class ObserverEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class ObserverEvaluator {
    private $event = null;
    private $data = null;
    private $operation = 72;

    /**
     * ObserverEvaluator constructor.
     * @param $event
     */
    public function __construct ($event) {
        $this->event = $event;
        $this->data = unserialize($event->data);
    }
     /**
     * check against all matching rules against the given observer event.
     * If rule found then validate the filter condition.
     */
    public function process () {
        if ( !$this->event->data_model ) return;

        $this->operation = $this->getOperationType();

        $rules = ObserverRule::with('active_actions')->active()
            ->where(function ($q) {
                $q->where('trigger_type_id', $this->operation)
                    ->orWhereNull('trigger_type_id');
            })
            ->where('model_id', $this->event->data_model->id)
            ->get();

        foreach ( $rules as $rule )
            $this->processRule($rule);
    }

    /**
     * Validate the observer event against a setup rule
     * @param $rule
     * @return mixed|null|void
     */
    private function processRule ($rule) {
        $data = $model = $this->data;
        $answer = false;

        $rule->filter_condition = $rule->filter_condition ? : true;

        $validationString = 'if(' . $rule->filter_condition . ') $answer = true;';
        eval($validationString);

        if ( !$answer ) return;

        foreach ( $rule->active_actions as $action ) {
            if ( $action->script_id )
                $this->processAction($action);
        }
    }

    /**
     * @param $action
     */
    private function processAction ($action) {
        $data = $model = $this->data;
        try {
            eval($action->script->script);
        } catch ( \Exception $e ) {
            //the exception is to be here
        }
    }

    /**
     * @return int
     */
    private function getOperationType () {
        $data = $this->data;

        if ( $data->isNewRecord() ) return 71;

        if ( $data->isTrashed() ) return 73;

        return 72;
    }

    /**
     * @param $data
     */
    public function devHandler ($data) {
    }

    public function __destruct () {

    }
}
