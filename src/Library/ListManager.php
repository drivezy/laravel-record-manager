<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Message;

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
                if ( !$data ) {
                    Message::info('Relationship ' . $relationship . 'not found for one to one');
                    break;
                };

                //user does not have access to the model
                if ( !ModelManager::validateModelAccess($data->reference_model, ModelManager::READ) ) {
                    $message = 'Access to reference model ' . $base . '.' . $relationship . ' : ' . $data->reference_model_id;
                    $message .= $data->reference_model ? ' is prohibited ' : ' is not found';
                    Message::warn($message);

                    break;
                }

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
        if ( $this->grouping_column )
            return self::setGroupingData();

        if ( $this->aggregation_column )
            return self::setAggregationData();

        if ( $this->stats )
            return self::getStatsData();


        $sql = 'SELECT ' . $this->sql['columns'] . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        if ( $this->order ) {
            $sql .= ' ORDER BY ' . $this->order;
        }

        $sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->limit * ( $this->page - 1 );

        $this->data = DB::select(DB::raw($sql));

        //check if there is any data that has to be modelled for special columns
        $iterator = 0;
        foreach ( $this->data as $item ) {
            //post process the encrypted record
            foreach ( $this->encryptedColumns as $column )
                $this->data[ $iterator ]->{$column} = Crypt::decrypt($item->{$column});

            //post process the source record
            foreach ( $this->sourceColumns as $column ) {
                $sourceId = str_replace(last(explode('.', $column)), explode('_', last(explode('.', $column)))[0] . '_id', $column);
                $sourceRecord = RecordManagement::getSourceColumnValue($item->{$column}, $item->{$sourceId});

                $this->data[ $iterator ]->{$column} = 'Object-' . $sourceRecord[0];
                $this->data[ $iterator ]->{$sourceId} = 'Record-' . $sourceRecord[1];
            }

            ++$iterator;
        }
    }

    /**
     * Get the stats data as part of the list condition
     */
    private function getStatsData () {
        $sql = 'SELECT count(1) count FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];

        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        $sql .= $this->deletedQuery();

        $this->stats = [
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

        $sql .= $this->deletedQuery();

        $this->data = DB::select(DB::raw($sql));
    }

    /**
     * Adding support to group data based on some column
     */
    private function setGroupingData () {
        //get proper definition of the user column data
        $columns = explode(',', $this->grouping_column);
        $selects = '';
        foreach ( $columns as $column ) {
            if ( $selects )
                $selects .= ', ' . $column . ' as \'' . str_replace('`', '', $column) . '\'';
            else
                $selects .= $column . ' as \'' . str_replace('`', '', $column) . '\'';
        }

        //see if the operator for grouping is defined
        $grouper = $this->aggregation_operator ? $this->aggregation_operator . '(' . $this->aggregation_column . ')' . ' as ' . $this->aggregation_column : 'count(1) as count';

        $sql = 'SELECT ' . $selects . ' , ' . $grouper . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        $sql .= $this->deletedQuery();
        $sql .= ' group by ' . $this->grouping_column;

        if ( $this->order )
            $sql .= ' ORDER BY ' . $this->order;

        $sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->limit * ( $this->page - 1 );

        $this->data = DB::select(DB::raw($sql));

        //setup the stats data against the grouping
        $this->setGroupingDataStats();
    }

    /**
     * Get the count of records for stats for grouped data
     */
    private function setGroupingDataStats () {
        $sql = 'SELECT ' . $this->grouping_column . ' , count(1) count FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'];
        if ( $this->query )
            $sql .= ' and (' . $this->query . ')';

        $sql .= $this->deletedQuery();
        $sql .= ' group by ' . $this->grouping_column;

        $sql = 'SELECT COUNT(1) count FROM (' . $sql . ') a';
        $this->stats = [
            'total'  => DB::select(DB::raw($sql))[0]->count,
            'page'   => $this->page,
            'record' => $this->limit,
        ];
    }

    /**
     * Adds deleted_at query to the string if trashed is false
     * @return string
     */
    private function deletedQuery () {
        if ( !$this->trashed )
            return ' and `' . $this->base . '`.deleted_at is null';

        return '';
    }
}

