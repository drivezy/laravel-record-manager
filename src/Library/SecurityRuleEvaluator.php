<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Models\SecurityRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class SecurityRuleEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class SecurityRuleEvaluator
{

    private $auth = false;
    private $rule = null;
    private $data = null;

    /**
     * SecurityRuleEvaluator constructor.
     * @param SecurityRule $rule
     * @param $data the current state of the model onto which the rule is being processed
     */
    public function __construct ($rule, $data = null)
    {
        $this->auth = Auth::user();

        $this->rule = $rule;
        $this->data = $data;
    }

    /**
     * validate if the security rule is being passed by the system
     * If any one of the rule fails, the rule is passed void
     */
    public function process ()
    {
        if ( !self::evaluateRole() ) return false;

        if ( !self::evaluateFilterCondition() ) return false;

        if ( !self::evaluateAdvancedCondition() ) return false;

        return true;
    }

    /**
     * Check if the user has necessary roles
     * @return bool
     */
    private function evaluateRole ()
    {
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

    /**
     * @return bool
     */
    private function evaluateFilterCondition ()
    {
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
    private function evaluateAdvancedCondition ()
    {
        if ( !$this->rule->script_id ) return true;

        $user = $this->auth;
        $data = $this->data;

        $answer = true;
        eval($this->rule->script->script);

        return $answer;
    }
}
