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

    //routes related to notification
    Route::resource('notification', 'NotificationController');
    Route::resource('notificationRecipient', 'NotificationRecipientController');

    Route::resource('smsNotification', 'SMSNotificationController');
    Route::resource('pushNotification', 'PushNotificationController');
    Route::resource('emailNotification', 'EmailNotificationController');
    Route::resource('inAppNotification', 'InAppNotificationController');
    Route::resource('whatsAppNotification', 'WhatsAppNotificationController');

    Route::resource('notificationSubscriber', 'NotificationSubscriberController');
    Route::resource('notificationTrigger', 'NotificationTriggerController');

    Route::resource('smsMessage', "SMSMessageController");
    Route::resource('smsTemplate', 'SMSTemplateController');
    Route::resource('deviceToken', 'DeviceTokenController');

    Route::resource('whatsAppMessage', 'WhatsAppMessageController');
    Route::resource('whatsAppTemplate', 'WhatsAppTemplateController');

    Route::resource('inAppMessage', 'InAppMessageController');

    Route::post('auditLog/{id}', 'AuditLogController@getAuditLog');
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

Route::group(['namespace' => 'Drivezy\LaravelRecordManager\Controllers'], function () {
    Route::get('whatsAppCallback/{id}', 'WhatsAppMessageController@handleCallbackUrl');
});
