<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ListPreferenceController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class ListPreferenceController extends RecordController {
    /**
     * @var string
     */
    public $model = ListPreference::class;

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request) {
        //only user with permission form-configurator should be able to create list preference for all
        $isFormConfigurator = AccessManager::hasPermission('form-configurator');
        $request->user_id = $isFormConfigurator ? $request->user_id : Auth::id();

        //avoiding the duplicate record against each
        $preference = ListPreference::firstOrNew([
            'source_type' => $request->source_type,
            'source_id'   => $request->source_id,
            'user_id'     => $request->user_id,
            'name'        => $request->name,
        ]);

        $preference->query = $request->query;
        $preference->column_definition = $request->column_definition;

        $preference->save();

        //if the user wants to override to all users with the configuration
        if ( $isFormConfigurator && $request->override_all ) {
            ListPreference::where('source_type', $request->source_type)
                ->where('source_id', $request->source_id)
                ->where('name', $request->name)
                ->update([
                    'query'             => $request->query,
                    'column_definition' => $request->column_definition,
                ]);
        }

        return Response::json(['success' => true, 'response' => $preference]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update (Request $request, $id) {
        return AccessManager::unauthorizedAccess();
    }
}