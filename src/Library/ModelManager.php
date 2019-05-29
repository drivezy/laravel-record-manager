<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelAdmin\Library\UIActionManager;
use Drivezy\LaravelAdmin\Models\UIAction;
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
        $model = is_string($model) ? DataModel::with('roles')->where('model_hash', md5($model))->first() : $model;
        if ( !$model ) return [];

        $actions = [];

        //checking for read permission
        if ( self::validateModelAccess($model, self::EDIT) ) {
            array_push($actions, UIAction::with('execution_script')->find(2));
        }

        //check for addition permission
        if ( self::validateModelAccess($model, self::ADD) ) {
            array_push($actions, UIAction::with('execution_script')->find(1));
            array_push($actions, UIAction::with('execution_script')->find(5));
        }

        //check for the delete permission
        if ( self::validateModelAccess($model, self::DELETE) ) {
            array_push($actions, UIAction::with('execution_script')->find(3));
        }

        if ( sizeof($actions) )
            array_push($actions, UIAction::with('execution_script')->find(4));

        //get the custom UI actions
        return array_merge($actions, UIActionManager::getObjectUIActions(md5(DataModel::class), $model->id));
    }

    /**
     * @param $model
     * @param $operation
     * @return bool
     */
    public static function validateModelAccess ($model, $operation) {
        $model = is_string($model) ? DataModel::with('roles')->where('model_hash', md5($model))->first() : $model;
        if ( !$model ) return false;

        return sizeof($model->roles) ? self::validateRegulatedModel($model, $operation) : self::validateUnRegulatedModel($model, $operation);
    }

    /**
     * Validate if the model without any roles attached is approachable or not
     * @param $model
     * @param $operation
     * @return bool
     */
    private static function validateUnRegulatedModel ($model, $operation) {
        //non internal users are allowed
        if ( !AccessManager::hasRole('internal') ) return false;

        //only allow publicly allowed operation
        if ( strpos($model->allowed_permissions, $operation) === false ) return false;

        return true;
    }

    /**
     * validate if the model with role attached is regulated or not
     * @param $model
     * @param $operation
     * @return bool
     */
    private static function validateRegulatedModel ($model, $operation) {
        //find all roles matching the operation
        $roles = RoleAssignment::where('source_id', $model->id)
            ->where('source_type', md5(DataModel::class))
            ->where('scope', 'like', '%' . $operation . '%')
            ->pluck('role_id')->toArray();

        //validate if the user has the role or not
        return AccessManager::hasRole($roles) ? true : false;
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
            if ( !SecurityRuleManager::evaluateSecurityRules($securityRules[ $model->table_name ], $data) )
                return false;
        }

        return new ColumnManager(md5(DataModel::class), $model->id, [
            'rules' => $securityRules,
            'data'  => $data,
        ]);
    }
}
