<?php

use Drivezy\LaravelRecordManager\Library\DictionaryManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Illuminate\Database\Seeder;

/**
 * Class DataModelSeeder
 */
class DataModelSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () {
        $records = [
            [
                'name'                => 'User',
                'description'         => 'User defined in the system',
                'namespace'           => 'App',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'LookupType',
                'description'         => 'Different types of lookup supported by the system',
                'namespace'           => 'Drivezy\LaravelUtility\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'LookupValue',
                'description'         => 'Different values of lookup supported by the system',
                'namespace'           => 'Drivezy\LaravelUtility\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'Property',
                'description'         => 'Key value pairing up of the different types of identities',
                'namespace'           => 'Drivezy\LaravelUtility\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'Role',
                'description'         => 'Definition of roles',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'Permission',
                'description'         => 'Definition of permissions',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'RoleAssignment',
                'description'         => 'Mapping of role against multiple entities',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'ra-d',
            ],
            [
                'name'                => 'PermissionAssignment',
                'description'         => 'Mapping of permission against multiple entities',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'ra-d',
            ],
            [
                'name'                => 'Route',
                'description'         => 'Definition of all routes defined in the system',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'r-ed',
            ],
            [
                'name'                => 'UserGroup',
                'description'         => 'Definition of different types of groups',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'UserGroupMember',
                'description'         => 'Association of user against the group',
                'namespace'           => 'Drivezy\LaravelAccessManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'DataModel',
                'description'         => 'All the models that are defined in the system',
                'namespace'           => 'Drivezy\LaravelRecordManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'ModelColumn',
                'description'         => 'Columns associated the the model',
                'namespace'           => 'Drivezy\LaravelRecordManager\Models',
                'allowed_permissions' => 'raed',
            ],
            [
                'name'                => 'ModelRelationship',
                'description'         => 'Different methods defined against the model',
                'namespace'           => 'Drivezy\LaravelRecordManager\Models',
                'allowed_permissions' => 'raed',
            ],
        ];

        foreach ( $records as $record )
            DataModel::create($record);

        $dataModels = DataModel::get();
        foreach ( $dataModels as $model ) {
            ( new DictionaryManager($model) )->process();
        }
    }
}