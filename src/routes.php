<?php


Route::group(['namespace' => 'Drivezy\LaravelRecordManager\Controllers',
              'prefix'    => 'api/record'], function () {
    Route::resource('property', 'PropertyController');

    Route::resource('lookupType', 'LookupTypeController');
    Route::resource('lookupValue', 'LookupValueController');

    Route::resource('columnDefinition', 'ColumnDefinitionController');
    Route::resource('relationshipDefinition', 'RelationshipDefinitionController');

    Route::resource('dataModel', 'DataModelController');;
    Route::resource('column', 'ColumnController');

    Route::resource('modelColumn', 'ModelColumnController');
    Route::resource('modelRelationship', 'ModelRelationshipController');

    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
    Route::resource('roleAssignment', 'RoleAssignmentController');
    Route::resource('permissionAssignment', 'PermissionAssignmentController');

    Route::resource('userGroup', 'UserGroupController');
    Route::resource('userGroupMember', 'UserGroupMemberController');

    Route::resource('document', 'DocumentController');

    Route::resource('listPreference', 'ListPreferenceController');
    Route::resource('formPreference', 'FormPreferenceController');

    Route::resource('scriptType', 'ScriptTypeController');
    Route::resource('systemScript', 'SystemScriptController');

    Route::resource('securityRule', 'SecurityRuleController');
    Route::resource('clientScript', 'ClientScriptController');

    Route::resource('customForm', 'CustomFormController');
    Route::get('formDetails/{id}', 'CustomFormController@getFormDetails');
});
