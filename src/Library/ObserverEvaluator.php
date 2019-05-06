<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ObserverRule;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class ObserverEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class ObserverEvaluator {
    /**
     * @var int
     */
    private $operation = 72;
    /**
     * @var Eloquent|null
     */
    private $model = null;

    /**
     * ObserverEvaluator constructor.
     * @param Eloquent $model
     */
    public function __construct (Eloquent $model) {
        $this->model = $model;
    }

    /**
     * check against all matching rules against the given observer event.
     * If rule found then validate the filter condition.
     */
    public function process () {
        //check if observer events is supposed to be run against it
        if(!$this->model->observable) return false;

        //get the data model against which event has triggered
        $dataModel = DataModel::where('model_hash', $this->model->class_hash)->first();
        if ( !$dataModel ) return;

        //find all the rules which matches the given model and its given operation activity
        //it also picks up all records wherein operation type is not defined
        $rules = ObserverRule::with('active_actions')->active()
            ->where(function ($q) {
                $q->where('trigger_type_id', $this->getOperationType())
                    ->orWhereNull('trigger_type_id');
            })
            ->where('model_id', $dataModel->id)
            ->get();

        //process all rules individually
        foreach ( $rules as $rule )
            $this->processRule($rule);
    }

    /**
     * Validate the observer event against a setup rule
     * @param $rule
     * @return mixed|null|void
     */
    private function processRule ($rule) {
        $data = $model = $this->model;
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
        $data = $model = $this->model;
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
        //check if it is a new record
        if ( $this->model->isNewRecord() ) return 71;

        //check if the record is in deleted state
        if ( $this->model->isTrashed() ) return 73;

        //defaults to updating of record
        return 72;
    }

    /**
     * @param $data
     */
    public function devHandler ($data) {
    }

    /**
     *
     */
    public function __destruct () {

    }
}
