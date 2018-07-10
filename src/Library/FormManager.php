<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAccessManager\Models\PermissionAssignment;
use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Models\CustomForm;

/**
 * Class FormManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class FormManager {

    /**
     * @param $formId
     * @return bool
     */
    public static function validateFormAccess ($formId) {
        //get all the roles attached to the form
        $roles = RoleAssignment::where('source_type', CustomForm::class)->where('source_id', $formId)->pluck('role_id')->toArray();
        if ( AccessManager::hasRole($roles) ) return true;

        //get all the permissions attached to the form
        $permissions = PermissionAssignment::where('source_type', CustomForm::class)->where('source_id', $formId)->pluck('permission_id')->toArray();
        if ( AccessManager::hasPermission($permissions) ) return true;

        //if either of the entity is true then it violated security policy
        if ( sizeof($roles) || sizeof($permissions) ) return false;

        return true;
    }

    /**
     * @param CustomForm $form
     * @return bool|ColumnManager
     */
    public static function getFormDictionary (CustomForm $form) {
        //get all security rules attached to this model
        $securityRules = SecurityRuleManager::getFormSecurityRules($form);

        //check if the security rule is applied at table level
        if ( isset($securityRules[ $form->identifier ]) ) {
            //check if all the security rules are valid for the model
            if ( !self::evaluateSecurityRules($securityRules[ $form->identifier ]) )
                return false;
        }

        return new ColumnManager(CustomForm::class, $form->id, [
            'rules' => $securityRules,
            'data'  => null,
        ]);
    }
}