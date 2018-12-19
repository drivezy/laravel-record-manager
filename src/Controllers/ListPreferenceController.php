<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
            'source_type' => $request->get('source_type'),
            'source_id'   => $request->get('source_id'),
            'user_id'     => $request->get('user_id'),
            'name'        => $request->get('name'),
        ]);

        $preference->query = $request->get('query');
        $preference->column_definition = $request->get('column_definition');

        $preference->save();

        //if the user wants to override to all users with the configuration
        if ( $isFormConfigurator && $request->override_all ) {
            ListPreference::where('source_type', $request->get('source_type'))
                ->where('source_id', $request->get('source_id'))
                ->where('name', $request->get('name'))
                ->update([
                    'query'             => $request->get('query'),
                    'column_definition' => $request->get('column_definition'),
                ]);
        }

        return Response::json(['success' => true, 'response' => $preference]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update (Request $request, $id) {
        return invalid_operation();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy (Request $request, $id) {
        $record = ListPreference::find($id);

        //if record is not found, return invalid operation
        if ( !$record )
            return invalid_operation();

        //only user with permission form-configurator should be able to drop list preference for all
        $isFormConfigurator = AccessManager::hasPermission('form-configurator');

        //only user record or configurator should be able to drop the record
        if ( $isFormConfigurator || $record->user_id == Auth::id() )
            return parent::destroy($request, $id);

        return invalid_operation();
    }
}