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
     * Get the data from the system and then return the result as list
     * @return array
     */
    public function process ($id = null) {
        //validate if the cache is valid or not
        if ( !self::loadDataFromCache() ) {
            parent::process();

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
            'model_class'        => $this->model->namespace . '\\' . $this->model->name,
            'model_hash'         => $this->model->model_hash,
        ];
    }

    /**
     * Get the includes and check their necessary joins and segregate the data
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
                    ->where('reference_type_id', 41)
                    ->first();

                //relationship against that item is not found
                if ( !$data ) break;

                //user does not have access to the model
                if ( !ModelManager::validateModelAccess($data->reference_model, ModelManager::READ) )
                    break;

                //set up the joins against the necessary columns
                self::setupColumnJoins($model, $data, $base);

                //setting up the required documents
                $base .= '.' . $relationship;
                $model = $data->reference_model;

                self::setReadDictionary($base, $model);

                $this->relationships[ $base ] = $data;
            }
        }
    }


    /**
     * Load the results of the record as requested by the list condition
     */
    private function loadResults () {
        if ( $this->aggregation_column )
            return self::setAggregationData();

        if ( $this->stats ) {
            $this->stats = self::getStatsData();
        }

        $sql = 'SELECT ' . $this->sql['columns'] . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        $sql .= ' and `' . $this->base . '`.deleted_at is null';

        if ( $this->order ) {
            $sql .= ' ORDER BY ' . $this->order;
        }

        $sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->limit * ( $this->page - 1 );

        $this->data = DB::select(DB::raw($sql));
    }

    /**
     * Get the stats data as part of the list condition
     * @return array
     */
    private function getStatsData () {
        $sql = 'SELECT count(1) count FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ') and `' . $this->base . '`.deleted_at is null';
        else
            $sql .= ' and `' . $this->base . '`.deleted_at is null';

        return [
            'total'  => DB::select(DB::raw($sql))[0]->count,
            'page'   => $this->page,
            'record' => $this->limit,
        ];
    }

    /**
     * If aggregation operation has been requested then do the same
     */
    private function setAggregationData () {
        $sql = 'SELECT ' . $this->aggregation_operator . '(' . $this->aggregation_column . ')' . ' as ' . $this->aggregation_column . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        $sql .= ' and `' . $this->base . '`.deleted_at is null';
        $this->data = DB::select(DB::raw($sql));
    }
}

