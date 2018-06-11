<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;

/**
 * Class ModelManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class ModelManager {

    /**
     *
     */
    const READ = 'r';
    /**
     *
     */
    const EDIT = 'e';
    /**
     *
     */
    const ADD = 'a';
    /**
     *
     */
    const DELETE = 'd';

    /**
     * @param $model
     * @return array
     */
    public static function getModelActions ($model) {
        if ( !$model ) return [];

        $permissions = str_split($model->allowed_permissions);
        $actions = [];

        //checking for read permission
        if ( in_array(self::EDIT, $permissions) ) {
            if ( self::validateModelAccess($model, self::EDIT) ) {
                $action = ['name' => 'edit', 'parameter' => 'edit', 'icon' => 'fa-pencil', 'placement_id' => 167, 'active' => 1, 'display_order' => 2, 'multi_operation' => 1];
                array_push($actions, $action);
            }
        }

        //check for addition permission
        if ( in_array(self::ADD, $permissions) ) {
            if ( self::validateModelAccess($model, self::ADD) ) {
                $action = ['name' => 'add', 'parameter' => 'add', 'icon' => 'fa-plus', 'placement_id' => 168, 'active' => 1, 'display_order' => 0, 'multi_operation' => 0];
                array_push($actions, $action);

                $action = ['name' => 'copy', 'parameter' => 'copy', 'icon' => 'fa-files-o', 'placement_id' => 167, 'active' => 1, 'display_order' => 1, 'multi_operation' => 0];
                array_push($actions, $action);
            }
        }


        if ( in_array(self::DELETE, $permissions) ) {
            if ( self::validateModelAccess($model, self::DELETE) ) {
                $action = ['name' => 'delete', 'parameter' => 'delete', 'icon' => 'fa-trash', 'placement_id' => 167, 'active' => 1, 'display_order' => 3, 'multi_operation' => 1];
                array_push($actions, $action);
            }
        }

        return $actions;
    }

    /**
     * @param $model
     * @param $operation
     * @return bool
     */
    public static function validateModelAccess ($model, $operation) {
        $model = is_string($model) ? DataModel::where('model_hash', md5($model))->first() : $model;

        if ( !$model ) return false;

        $roles = RoleAssignment::where('source_id', $model->id)
            ->where('source_type', 'Model')
            ->where('scope', 'like', '%' . $operation . '%')
            ->pluck('role_id')->toArray();

        return AccessManager::hasRole($roles);
    }


    /**
     * @param $model
     * @param $data
     * @param $operation
     * @return array
     */
    public static function getModelDictionary ($model) {
        $dictionary = self::getDictionary($model);
        $columns = [];

        foreach ( $dictionary as $column ) {
            //check if there are any security rule against the column
            $column = self::evaluateColumnSecurityRule($column);
            if ( $column )
                array_push($columns, $column);

        }

        return $columns;
    }

    /**
     * @param $column
     * @param null $data
     * @param $operation
     * @return bool
     */
    private static function evaluateColumnSecurityRule ($column, $data = null
    ) {
        $rules = $column->read_security_rules;
        unset($column->read_security_rules);

        if ( !sizeof($rules) ) return $column;

        foreach ( $rules as $rule ) {
            $passed = ( new SecurityRuleEvaluator($rule, $data) )->process();
            if ( !$passed ) return false;
        }

        return $column;
    }

    /**
     * @param $model
     * @return ModelColumn[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getViewDictionary ($model) {
        return ModelColumn::with(['reference_model', 'read_security_rules'])->where('model_id', $model->id)->get();
    }
}