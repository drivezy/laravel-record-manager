<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\BusinessRule;

/**
 * Class BusinessRuleManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class BusinessRuleManager {

    /**
     * BusinessRuleManager constructor.
     */
    public function __construct () {
    }

    /**
     * @param $model
     */
    public static function getQueryStrings ($model) {
        $rules = BusinessRule::where('model_id', $model->id)
            ->where('active', true)
            ->where('on_query', true)
            ->orderBy('order', 'asc')
            ->get();

        foreach ( $rules as $rule ) {
            if ( !( new BusinessRuleEvaluator($rule) )->process() )
                continue;

            $query = null;
            eval($rule->script->script);
        }
    }
}