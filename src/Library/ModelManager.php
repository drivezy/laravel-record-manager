<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Models\DataModel;

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
        if ( in_array(EDIT, $permissions) ) {
            if ( self::validateModelAccess($model, EDIT) ) {
                $action = ['name' => 'edit', 'parameter' => 'edit', 'icon' => 'fa-pencil', 'placement_id' => 167, 'active' => 1, 'display_order' => 2, 'multi_operation' => 1];
                array_push($actions, $action);
            }
        }

        //check for addition permission
        if ( in_array(ADD, $permissions) ) {
            if ( self::validateModelAccess($model, ADD) ) {
                $action = ['name' => 'add', 'parameter' => 'add', 'icon' => 'fa-plus', 'placement_id' => 168, 'active' => 1, 'display_order' => 0, 'multi_operation' => 0];
                array_push($actions, $action);

                $action = ['name' => 'copy', 'parameter' => 'copy', 'icon' => 'fa-files-o', 'placement_id' => 167, 'active' => 1, 'display_order' => 1, 'multi_operation' => 0];
                array_push($actions, $action);
            }
        }


        if ( in_array(DELETE, $permissions) ) {
            if ( self::validateModelAccess($model, DELETE) ) {
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

        if ( !$model )
            return AccessManager::unauthorizedAccess();

        $roles = RoleAssignment::where('source_id', $model->id)
            ->where('source_type', 'Model')
            ->where('scope', 'like', '%' . $operation . '%')
            ->pluck('role_id');

        return AccessManager::hasRole($roles);
    }
}