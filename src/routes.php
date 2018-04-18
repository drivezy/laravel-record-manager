<?php


Route::group(['namespace' => 'Drivezy\LaravelRecordManager\Controllers',
              'prefix'    => 'api/record'], function () {
    Route::resource('property', 'PropertyController');

    Route::resource('lookupType', 'LookupTypeController');
    Route::resource('lookupValue', 'LookupValueController');

    Route::resource('dataModel', 'DataModelController');;
    Route::resource('modelColumn', 'ModelColumnController');
    Route::resource('modelRelationship', 'ModelRelationshipController');

    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
    Route::resource('roleAssignment', 'RoleAssignmentController');
    Route::resource('permissionAssignment', 'PermissionAssignmentController');

    Route::resource('userGroup', 'UserGroupController');
    Route::resource('userGroupMember', 'UserGroupMemberController');

    Route::resource('document', 'DocumentController');
});
