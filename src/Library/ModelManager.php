<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Controllers\BaseController;
use Drivezy\LaravelRecordManager\Models\ClientScript;
use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelUtility\Models\BaseModel;

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

        if ( strpos($model->allowed_permissions, $operation) === false ) return false;

        $roles = RoleAssignment::where('source_id', $model->id)
            ->where('source_type', 'Model')
            ->where('scope', 'like', '%' . $operation . '%')
            ->pluck('role_id')->toArray();

        return AccessManager::hasRole($roles) ? 'yes' : 'no';
    }


    /**
     * @param $model
     * @param $operation
     * @param null $data
     * @return bool|ColumnManager
     */
    public static function getModelDictionary ($model, $operation, $data = null) {
        //get all security rules attached to this model
        $securityRules = SecurityRuleManager::getModelSecurityRules($model, $operation);

        //check if the security rule is applied at table level
        if ( isset($securityRules[ $model->table_name ]) ) {
            //check if all the security rules are valid for the model
            if ( !self::evaluateSecurityRules($securityRules[ $model->table_name ]) )
                return false;
        }

        return new ColumnManager(DataModel::class, $model->id, [
            'rules' => $securityRules,
            'data'  => $data,
        ]);
    }
}