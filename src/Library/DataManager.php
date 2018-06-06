<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Class DataManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class DataManager {
    protected $includes, $sqlCacheIdentifier = false;

    protected $model, $base, $data;

    protected $dictionary = [];
    protected $relationships = [];
    protected $layout = [];
    protected $joins = [];
    protected $tables = [];
    protected $sql = [];

    protected $stats, $order = false;
    protected $limit = 20;
    protected $page = 1;

    /**
     * DataManager constructor.
     * @param $model
     * @param array $args
     */
    public function __construct ($model, $args = []) {
        $this->model = $model;
        $this->model->actions = ModelManager::getModelActions($model);

        foreach ( $args as $key => $value ) {
            $this->{$key} = $value;
        }

        $this->base = strtolower($model->name);

        $this->dictionary[ $this->base ] = ModelColumn::with('reference_model')->where('model_id', $this->model->id)->get();

        $this->relationships[ $this->base ] = $model;
        $this->relationships[ $this->base ]['form_layouts'] = PreferenceManager::getFormPreference(DataModel::class, $this->model->id);

        $this->tables[ $this->base ] = $model->table_name;
    }

    /**
     * This will create the join condition for the alias as part of its relationship with the parent one
     * @param $relationship
     * @param $base
     */
    protected function setupColumnJoins ($relationship, $base) {
        $sourceColumn = $relationship->source_column ? $relationship->source_column->name : 'id';
        $aliasColumn = $relationship->alias_column ? $relationship->alias_column->name : 'id';
        //setup for the default column joins
        $join = '`' . $base . '`.' . $sourceColumn . ' = ';
        $join .= '`' . $base . '.' . $relationship->name . '`.' . $aliasColumn;
        array_push($this->joins, $join);

        //check for additional definition
        $join = str_replace('current', '`' . $base . '`', $relationship->join_definition);
        $join = str_replace('alias', '`' . $base . '.' . $relationship->name . '`', $join);
        array_push($this->joins, $join);
    }

    /**
     * Get the select items which are to be part of the record
     * Also create necessary alias and the return element
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
     * Get the select items which are part of the requested layout
     * Also load the parent items part of the dictionary
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
     * Check if the cache against the sql conditions is present
     * If yes then load back to the system
     * @return array|bool|mixed
     */
    protected function loadDataFromCache () {
        if ( !$this->sqlCacheIdentifier ) return false;

        $record = Cache::get($this->sqlCacheIdentifier, false);
        if ( !$record ) return false;

        $this->sql = $record->sql;

        return true;
    }

    /**
     * Create the sql join against the tables that are attached as part of the inclusions
     * This is part of the where condition
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
     * create array of  necessary join conditions against the tables that are part of the includes.
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
     * Create the data related to base query excluding the restrictive condition
     * Then save it to the cache so that it can be fetched
     * back without need of too much query iteration
     */
    protected function constructQuery () {
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

}