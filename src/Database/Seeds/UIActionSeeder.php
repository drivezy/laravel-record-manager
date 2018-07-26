<?php

namespace Drivezy\LaravelRecordManager\Database\Seeds;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\SystemScript;
use Drivezy\LaravelRecordManager\Models\UIAction;

/**
 * Class UIActionSeeder
 * @package Drivezy\LaravelRecordManager\Database\Seeds
 */
class UIActionSeeder {

    /**
     *
     */
    public function run () {
        $records = [
            [
                'id'                  => 1,
                'name'                => 'Add Generic Record',
                'identifier'          => 'addGeneric',
                'description'         => 'Add generic record',
                'display_order'       => '1',
                'image'               => 'fa-plus',
                'as_header'           => 1,
                'as_footer'           => 0,
                'as_dropdown'         => 0,
                'as_context'          => 0,
                'as_record'           => 0,
                'execution_script_id' => null,
                'filter_condition_id' => null,
            ],
            [
                'id'                  => 2,
                'name'                => 'Edit Generic Record',
                'identifier'          => 'editGeneric',
                'description'         => 'Edit generic record',
                'display_order'       => '1',
                'image'               => 'fa-pencil',
                'as_header'           => 0,
                'as_footer'           => 0,
                'as_dropdown'         => 0,
                'as_context'          => 1,
                'as_record'           => 1,
                'execution_script_id' => null,
                'filter_condition_id' => null,
            ],
            [
                'id'                  => 3,
                'name'                => 'Delete Generic Record',
                'identifier'          => 'deleteGeneric',
                'description'         => 'Delete generic record',
                'display_order'       => '2',
                'image'               => '',
                'as_header'           => 0,
                'as_footer'           => 0,
                'as_dropdown'         => 0,
                'as_context'          => 1,
                'as_record'           => 1,
                'execution_script_id' => null,
                'filter_condition_id' => null,
            ],
            [
                'id'                  => 4,
                'name'                => 'Audit Generic Record',
                'identifier'          => 'auditGeneric',
                'description'         => 'Add generic record',
                'display_order'       => '1',
                'image'               => '',
                'as_header'           => 1,
                'as_footer'           => 0,
                'as_dropdown'         => 0,
                'as_context'          => 1,
                'as_record'           => 0,
                'execution_script_id' => null,
                'filter_condition_id' => null,
            ],
        ];

        foreach ( $records as $record ) {
            $record['source_type'] = md5(DataModel::class);
            $record['source_id'] = 0;

            UIAction::create($record);
        }
    }

    /**
     *
     */
    public function drop () {
    }
}