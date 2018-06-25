<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ClientScript;

/**
 * Class ClientScriptManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class ClientScriptManager {

    /**
     * @param $identifier
     * @return array
     */
    public static function getClientScripts ($identifier) {
        $scripts = ClientScript::with('script')->where('name', 'LIKE', '' . $identifier . '%')->get();

        $records = [];
        foreach ( $scripts as $script ) {
            array_push($records, [
                'name'             => $script->name,
                'activity_type_id' => $script->activity_type_id,
                'script'           => $script->script->script,
                'column'           => $script->activity_type_id == 2 ? last(explode('.', $script->name)) : null,
            ]);
        }

        return $records;
    }
}