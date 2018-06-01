<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class ListManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class ListManager {

    private $stats, $query, $includes, $order, $sqlCacheIdentifier = false;
    private $limit = 20;
    private $page = 1;

    private $model;
    private $base;

    private $dictionary = [];
    private $relationships = [];
    private $layout = [];
    private $joins = [];
    private $tables = [];
    private $sql = [];
    /**
     * @var
     */
    private $data;

    public function __construct ($model, $args = []) {
        $this->model = $model;
        $this->model->actions = ModelManager::getModelActions($model);

        foreach ( $args as $key => $value ) {
            $this->{$key} = $value;
        }

        $this->base = strtolower($model->name);

        $this->dictionary[ $this->base ] = ModelColumn::with('reference_model')->where('model_id', $this->model->id)->get();
        $this->relationships[ $this->base ] = $model;
        $this->tables[ $this->base ] = $model->table_name;
    }

    /**
     *
     */
    public function process () {
        if ( !self::loadDataFromCache() ) {
            self::processIncludes();
            self::constructQuery();
        }
        self::loadResults();

        return [
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
     * @param $relationship
     * @param $base
     */
    private function setupColumnJoins ($relationship, $base) {
        //setup for the default column joins
        $join = '`' . $base . '`.' . $relationship->source_column->name . ' = ';
        $aliasColumn = $relationship->alias_column ? $relationship->alias_column->name : 'id';
        $join .= '`' . $base . '.' . $relationship->name . '`.' . $aliasColumn;

        array_push($this->joins, $join);
        //check for additional definition
        $join = str_replace('current', '`' . $base . '`', $relationship->join_definition);
        $join = str_replace('alias', '`' . $base . '.' . $relationship->name . '`', $join);
        array_push($this->joins, $join);
    }

    /**
     *
     */
    private function constructQuery () {
        $this->sql['columns'] = self::getSelectItems();
        $this->sql['tables'] = self::getTableDefinitions();
        $this->sql['joins'] = self::getJoins() ? : ' 1 = 1';

        $this->sqlCacheIdentifier = md5($this->model->model_hash . '-' . microtime('true') . '-' . md5($this->includes));
        Cache::put($this->sqlCacheIdentifier, (object) [
            'user_id' => Auth::id(),
            'sql'     => $this->sql,
            'time'    => strtotime('now'),
        ], 30);
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
     * @return string
     */
    private function getSelectItems () {
        self::fixSelectItems();
        $query = '';
        foreach ( $this->layout as $key => $value ) {
            if ( !$query )
                $query = $value . ' as \'' . $key . '\'';
            else
                $query .= ', ' . $value . ' as \'' . $key . '\'';
        }

        return $query;
    }

    /**
     * @return string
     */
    private function getTableDefinitions () {
        $query = '';
        foreach ( $this->tables as $key => $value ) {
            if ( !$query )
                $query = $value . ' `' . $key . '` ';
            else
                $query .= ', ' . $value . ' `' . $key . '` ';
        }

        return $query;
    }

    /**
     * @return mixed|string
     */
    private function getJoins () {
        $query = '';
        foreach ( $this->joins as $join ) {
            if ( !$join ) continue;

            if ( !$query )
                $query = $join;
            else
                $query .= ' AND ' . $join;
        }

        return $query;
    }

    /**
     *
     */
    private function fixSelectItems () {
        $columns = [];
        foreach ( $this->dictionary[ $this->base ] as $item ) {
            $columns[ $this->base . '.' . $item->name ] = '`' . $this->base . '`.' . $item->name;
        }

        foreach ( $this->layout as $item ) {
            $columns[ $item['object'] . '.' . $item['column'] ] = '`' . $item['object'] . '`.' . $item['column'];
        }

        foreach ( $this->relationships as $key => $value ) {
            $columns[ $key . '.id' ] = '`' . $key . '`.id';
        }
        $this->layout = $columns;
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

    /**
     * @return array|bool|mixed
     */
    private function loadDataFromCache () {
        if ( !$this->sqlCacheIdentifier ) return false;

        $record = Cache::get($this->sqlCacheIdentifier, false);
        if ( !$record ) return false;

        $this->sql = $record->sql;

        return true;
    }


}

