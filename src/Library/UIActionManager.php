<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\UIAction;

/**
 * Class UIActionManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class UIActionManager {

    /**
     * @param $source
     * @param $id
     * @return \Illuminate\Support\Collection
     */
    public static function getObjectUIActions ($source, $id) {
        return UIAction::with('execution_script')->where('source_type', $source)->where('source_id', $id)->get();
    }
}