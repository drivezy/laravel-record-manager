<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Illuminate\Support\Facades\DB;

/**
 * Class ListManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class ListManager extends DataManager {

    /**
     * @return array
     */
    public function process () {
        if ( !self::loadDataFromCache() ) {
            self::processIncludes();
            self::constructQuery();
        }
        self::loadResults();

        return [
            'base'               => $this->base,
            'data'               => $this->data,
            'stats'              => $this->stats,
            'relationship'       => $this->relationships,
            'dictionary'         => $this->dictionary,
            'request_identifier' => $this->sqlCacheIdentifier,
        ];
    }

    /**
     * @return bool
     */
    private function processIncludes () {
        if ( !$this->includes ) return true;

        $includes = explode(',', $this->includes);
        foreach ( $includes as $include ) {
            $relationships = explode('.', $include);

            $model = $this->model;
            $base = $this->base;

            foreach ( $relationships as $relationship ) {
                $data = ModelRelationship::with(['reference_model', 'source_column', 'alias_column'])
                    ->where('model_id', $model->id)->where('name', $relationship)
                    ->where('reference_type_id', 1)
                    ->first();

                //relationship against that item is not found
                if ( !$data ) break;

                //user does not have access to the model
                if ( !ModelManager::validateModelAccess($data->reference_model, ModelManager::READ) )
                    break;

                //set up the joins against the necessary columns
                self::setupColumnJoins($data, $base);

                //setting up the required documents
                $base .= '.' . $relationship;
                $model = $data->reference_model;

                $this->relationships[ $base ] = $data;
                $this->dictionary[ $base ] = ModelColumn::where('model_id', $data->reference_model_id)->get();
                $this->tables[ $base ] = $data->reference_model->table_name;
            }
        }
    }


    /**
     *
     */
    private function loadResults () {
        if ( $this->stats ) {
            $this->stats = self::getStatsData();
        }

        $sql = 'SELECT ' . $this->sql['columns'] . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        if ( $this->order ) {
            $sql .= ' ORDER BY ' . $this->order;
        }

        $sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->limit * ( $this->page - 1 );

        $this->data = DB::select(DB::raw($sql));
    }

    /**
     * @return array
     */
    private function getStatsData () {
        $sql = 'SELECT count(1) count FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        return [
            'total'  => DB::select(DB::raw($sql))[0]->count,
            'page'   => $this->page,
            'record' => $this->limit,
        ];
    }
}

