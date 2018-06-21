<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Http\Request;

/**
 * Class AdminResponseManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class AdminResponseManager {

    private $request = null;
    private $model = null;

    /**
     * AdminResponseManager constructor.
     * @param Request $request
     * @param DataModel $model
     */
    public function __construct (Request $request, DataModel $model) {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     *
     */
    public function index () {
        $request = $this->request;

        $records = ( new ListManager($this->model, [
            'includes'             => $request->has('includes') ? $request->get('includes') : false,
            'list_layouts'         => self::getLayoutDefinition(),
            'stats'                => $request->has('stats') ? $request->get('stats') : false,
            'query'                => $request->has('query') ? $request->get('query') : false,
            'sqlCacheIdentifier'   => $request->has('request_identifier') ? $request->get('request_identifier') : false,
            'limit'                => $request->has('limit') ? $request->get('limit') : 20,
            'page'                 => $request->has('page') ? $request->get('page') : 1,
            'aggregation_column'   => $request->has('aggregation_column') ? $request->get('aggregation_column') : null,
            'aggregation_operator' => $request->has('aggregation_operator') ? $request->get('aggregation_operator') : null,
        ]) )->process();

        return success_response($records);
    }

    /**
     * @param $id
     */
    public function show ($id) {
        $request = $this->request;

        $records = ( new RecordManager($this->model, [
            'includes'           => $request->has('includes') ? $request->get('includes') : false,
            'list_layouts'       => self::getLayoutDefinition(),
            'sqlCacheIdentifier' => $request->has('request_identifier') ? $request->get('request_identifier') : false,
        ]) )->process($id);

        return success_response($records);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getLayoutDefinition () {
        $columns = [];
        if ( !$this->request->has('layout_id') ) return $columns;

        $definition = ListPreference::find($this->request->get('layout_id'));
        $definition = json_decode($definition->column_definition, true);

        foreach ( $definition as $item ) {
            array_push($columns, [
                'object' => $item['object'],
                'column' => $item['column'],
            ]);
        }

        return $columns;
    }
}