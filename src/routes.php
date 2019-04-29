<?php


Route::group(['namespace' => 'Drivezy\LaravelRecordManager\Controllers',
              'prefix'    => 'api/record'], function () {
    Route::resource('property', 'PropertyController');

    Route::resource('lookupType', 'LookupTypeController');
    Route::resource('lookupValue', 'LookupValueController');

    Route::resource('columnDefinition', 'ColumnDefinitionController');
    Route::resource('relationshipDefinition', 'RelationshipDefinitionController');

    Route::resource('dataModel', 'DataModelController');
    Route::get('sourceColumnDetail', 'DataModelController@getSourceColumnDetails');
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

    Route::resource('systemScript', 'SystemScriptController');

    Route::resource('securityRule', 'SecurityRuleController');
    Route::resource('businessRule', 'BusinessRuleController');

    Route::resource('observerRule', 'ObserverRuleController');
    Route::resource('observerAction', 'ObserverActionController');

    //routes related to server deployments and its prov
    Route::resource('serverDeployment', 'ServerDeploymentController');
    Route::resource('codeDeployment', 'CodeDeploymentController');
    Route::resource('codeCommit', 'CodeCommitController');
});


Route::group(['namespace'  => 'Drivezy\LaravelRecordManager\Controllers',
              'prefix'     => 'internal',
              'middleware' => 'internal'], function () {

    Route::post('codeDeployment', 'CodeDeploymentController@store');
    Route::post('serverDeployment', 'ServerDeploymentController@store');
});

Route::group(['namespace' => 'Drivezy\LaravelRecordManager\Controllers',
              'prefix'    => 'vendor'], function () {

    Route::post('bitbucketCodePush/{key}', 'CodeCommitController@logBitBucketCommit');
});
