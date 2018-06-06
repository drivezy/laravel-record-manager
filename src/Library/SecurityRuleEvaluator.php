<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\SecurityRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class SecurityRuleEvaluator
 * @package Drivezy\LaravelRecordManager\Library
 */
class SecurityRuleEvaluator {
    /**
     * @var bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private $auth = false;
    /**
     * @var SecurityRule|null
     */
    private $rule = null;
    /**
     * @var null
     */
    private $data = null;

    /**
     * SecurityRuleEvaluator constructor.
     * @param SecurityRule $rule
     * @param $data
     */
    public function __construct (SecurityRule $rule, $data) {
        $this->auth = Auth::user();

        $this->rule = $rule;
        $this->data = $data;
    }

    /**
     *
     */
    public function process () {
        
    }
}