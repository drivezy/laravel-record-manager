<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\FormPreference;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Support\Facades\Auth;

/**
 * Class PreferenceManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class PreferenceManager {

    /**
     * @param $source
     * @param $id
     * @return mixed
     */
    public static function getListPreference ($source, $id) {
        $userId = Auth::id();

        $listPreferences = ListPreference::where('source_type', $source)
            ->where('source_id', $id)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereNull($userId);
            })->get();

        return $listPreferences;
    }

    /**
     * @param $source
     * @param $id
     * @return mixed
     */
    public static function getFormPreference ($source, $id) {
        $userId = Auth::id();

        $formPreference = FormPreference::where('source_type', $source)
            ->where('source_id', $id)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereNull($userId);
            })->get();

        return $formPreference;
    }

}