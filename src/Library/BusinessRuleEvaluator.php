<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Models\BusinessRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class BusinessRuleEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class BusinessRuleEvaluator {
    /**
     * @var bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private $auth = false;
    /**
     * @var SecurityRule|null
     */
    private $rule = null;
    /**
     * @var the|null
     */
    private $data = null;

    /**
     * BusinessRuleEvaluator constructor.
     * @param BusinessRule $rule
     * @param null $data
     */
    public function __construct (BusinessRule $rule, $data = null) {
        $this->auth = Auth::user();

        $this->rule = $rule;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function process () {
        if ( !$this->evaluateFilterCondition() )
            return false;

        if ( !$this->evaluateRole() )
            return false;

        return true;
    }

    /**
     * @return bool
     */
    private function evaluateFilterCondition () {
        if ( !$this->rule->filter_condition ) return true;

        $data = $this->data;
        $user = $this->auth;

        $answer = false;

        $validationString = 'if(' . $this->rule->filter_condition . ') $answer = true;';
        eval($validationString);

        return $answer;
    }

    /**
     * @return bool
     */
    private function evaluateRole () {
        $roles = $this->rule->roles;

        //check if there were any role attached to the rule
        //if empty then evaluate to true
        if ( !sizeof($roles) ) return true;

        $roles = [];
        foreach ( $roles as $role ) {
            array_push($roles, $role->role_id);
        }

        return AccessManager::hasRole($roles);
    }


}