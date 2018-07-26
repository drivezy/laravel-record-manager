<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelAdmin\Models\CustomForm;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\SecurityRule;
use Drivezy\LaravelRecordManager\Models\SystemScript;
use Illuminate\Support\Facades\DB;

/**
 * Class SecurityRuleManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class SecurityRuleManager {
    /**
     * @param DataModel $model
     * @param $operation
     * @return array
     */
    public static function getModelSecurityRules (DataModel $model, $operation) {
        $rules = [];

        $query = "SELECT id, name, filter_condition, script_id 
                    FROM dz_security_rules 
                    WHERE deleted_at is null AND active = 1 AND operation like '%$operation%' AND (name = '" . $model->table_name . "' OR name = CONCAT('" . $model->table_name . "', '.*'))
                  UNION DISTINCT
                  SELECT a.id, a.name, a.filter_condition, a.script_id 
                    FROM dz_security_rules a, dz_column_details b 
                    WHERE b.source_id = $model->id AND b.source_type != '" . md5(CustomForm::class) . "' AND a.active = 1 AND a.operation like '%$operation%' AND (a.name = CONCAT('*.', b.name) OR a.name = CONCAT('" . $model->table_name . "', '.', b.name)) 
                    AND a.deleted_at is null AND b.deleted_at is null;";


        $records = DB::select(DB::raw($query));
        foreach ( $records as $record ) {
            //get the grouping criteria
            $base = self::getModelSecurityObjectNotation($model->table_name, $record->name);

            if ( !isset($rules[ $base ]) ) $rules[ $base ] = [];

            if ( $record->script_id )
                $record->script = SystemScript::find($record->script_id);

            $record->roles = self::getRoles($record->id);

            array_push($rules[ $base ], $record);
        }

        return $rules;
    }

    /**
     * @param Form $form
     * @return array
     */
    public static function getFormSecurityRules (CustomForm $form) {
        $rules = [];
        $name = 'form_' . $form->id;

        $query = "SELECT id, name, filter_condition, script_id 
                    FROM dz_security_rules 
                    WHERE deleted_at is null AND active = 1 AND (name = '" . $form->identifier . "' OR name = CONCAT('" . $name . "', '.*'))
                  UNION DISTINCT
                  SELECT a.id, a.name, a.filter_condition, a.script_id 
                    FROM dz_security_rules a, dz_column_details b 
                    WHERE b.source_id = $form->id AND a.active = 1 AND (a.name = CONCAT('*.', b.name) OR a.name = CONCAT('" . $name . "', '.', b.name))
                    AND b.source_type = '" . md5(CustomForm::class) . "' 
                    AND a.deleted_at is null AND b.deleted_at is null;";


        $records = DB::select(DB::raw($query));
        foreach ( $records as $record ) {
            //get the grouping criteria
            $base = self::getModelSecurityObjectNotation($name, $record->name);

            if ( !isset($rules[ $base ]) ) $rules[ $base ] = [];

            if ( $record->script_id )
                $record->script = SystemScript::find($record->script_id);

            $record->roles = self::getRoles($record->id);

            array_push($rules[ $base ], $record);
        }

        return $rules;
    }

    /**
     * @param $tableName
     * @param $name
     * @return mixed
     */
    private static function getModelSecurityObjectNotation ($tableName, $name) {
        if ( $name == $tableName || $name == $tableName . '.*' )
            return $tableName;

        $splits = explode('.', $name);

        return end($splits);
    }

    /**
     * @param $id
     * @return mixed
     */
    private static function getRoles ($id) {
        return RoleAssignment::where('source_type', md5(SecurityRule::class))->where('source_id', $id)->get();
    }

    /**
     * @param array $rules
     * @param null $data
     * @return bool
     */
    public static function evaluateSecurityRules (array $rules, $data = null) {
        foreach ( $rules as $rule ) {
            $passed = ( new SecurityRuleEvaluator($rule, $data) )->process();
            if ( !$passed ) return false;
        }

        return true;
    }
}