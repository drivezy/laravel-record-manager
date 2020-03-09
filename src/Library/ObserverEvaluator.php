<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Library\Notification\NotificationManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ObserverAction;
use Drivezy\LaravelRecordManager\Models\ObserverRule;
use Exception;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class ObserverEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class ObserverEvaluator
{
    /**
     * @var Eloquent|null
     */
    private $model = null;

    /**
     * ObserverEvaluator constructor.
     * @param Eloquent $model
     */
    public function __construct (Eloquent $model)
    {
        $this->model = $model;
    }

    /**
     * check against all matching rules against the given observer event.
     * If rule found then validate the filter condition.
     */
    public function process ()
    {
        //check if observer events is supposed to be run against it
        //@todo bring in this feature. right now commenting it
//        if ( !$this->model->observable ) return false;

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
     * @return int
     */
    private function getOperationType ()
    {
        //check if it is a new record
        if ( $this->model->isNewRecord() ) return 71;

        //check if the record is in deleted state
        if ( $this->model->isTrashed() ) return 73;

        //defaults to updating of record
        return 72;
    }

    /**
     * Validate the observer event against a setup rule
     * @param $rule
     * @return mixed|null|void
     */
    private function processRule ($rule)
    {
        $data = $model = $this->model;
        $answer = false;

        //see if there is any filter condition defined under this action
        $rule->filter_condition = $rule->filter_condition ? : true;

        $validationString = 'if(' . $rule->filter_condition . ') $answer = true;';
        eval($validationString);

        if ( !$answer ) return;

        //process actions against the validated rule
        foreach ( $rule->active_actions as $action ) {
            //run the script if any attached as part of the action
            if ( $action->script_id )
                $this->processAction($action);

            //run the notification if any attached as part of the action
            if ( $action->notification_id )
                $this->processNotification($action);
        }
    }

    /**
     * eval the script that is going as part of this action
     * @param ObserverAction $action
     */
    private function processAction (ObserverAction $action)
    {
        $data = $model = $this->model;
        try {
            eval($action->script->script);
        } catch ( Exception $e ) {
            //the exception is to be here
        }
    }

    /**
     * process the notification against the given action
     * @param ObserverAction $action
     */
    private function processNotification (ObserverAction $action)
    {
        ( new NotificationManager($action->notification_id) )->process($this->model->id);
    }
}
