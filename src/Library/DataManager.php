<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\Facade\Message;
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
    protected $rejectedColumns = [];
    protected $acceptedColumns = [];
    protected $encryptedColumns = [];
    protected $sourceColumns = [];

    protected $relationships = [];
    protected $layout = [];
    protected $restrictions = [];
    protected $tables = [];
    protected $sql = [];

    protected $aggregation_operator = null;
    protected $aggregation_column = null;

    protected $stats, $order = false;
    protected $limit = 20;
    protected $page = 1;
    protected $trashed = false;

    /**
     * DataManager constructor.
     * @param $model
     * @param array $args
     */
    public function __construct ($model, $args = []) {
        $this->model = $model;

        foreach ( $args as $key => $value ) {
            $this->{$key} = $value;
        }
        $this->base = strtolower($this->model->name);
    }

    /**
     * @param null $id
     */
    public function process ($id = null) {
        $this->model->actions = ModelManager::getModelActions($this->model);

        self::setReadDictionary($this->base, $this->model);

        $this->relationships[ $this->base ] = $this->model;
        $this->relationships[ $this->base ]['form_layouts'] = PreferenceManager::getFormPreference(md5(DataModel::class), $this->model->id);

        $this->tables[ $this->base ] = $this->model->table_name;

        $this->tables[ $this->base ] = [
            'table' => $this->model->table_name,
            'join'  => null,
        ];

        //add all restrictions that is part of the parent
        foreach ( $this->setQueryRestriction($this->model, $this->base) as $restriction )
            array_push($this->restrictions, $restriction);

        if ( !$this->trashed )
            array_push($this->restrictions, '`' . $this->base . '`.deleted_at is null');
    }

    /**
     * This will create the join condition for the alias as part of its relationship with the parent one
     * @param $model
     * @param $relationship
     * @param $base
     */
    protected function setupColumnJoins ($model, $relationship, $base) {
        //get the source model definition
        $source = (object) [
            'base'   => $base,
            'table'  => $model->table_name,
            'column' => $relationship->source_column ? $relationship->source_column->name : 'id',
        ];

        //get the related model definition
        $alias = (object) [
            'base'   => $base . '.' . $relationship->name,
            'table'  => $relationship->reference_model->table_name,
            'column' => $relationship->alias_column ? $relationship->alias_column->name : 'id',
        ];


        //get the default join condition
        $joinCondition = '`' . $source->base . '`.' . $source->column . ' = `' . $alias->base . '`.' . $alias->column;
        if ( !$this->trashed )
            $joinCondition .= ' and `' . $alias->base . '`.deleted_at is null';

        //check for additional join condition
        if ( $relationship->join_definition ) {
            $join = str_replace('current', '`' . $source->base . '`', $relationship->join_definition);
            $join = str_replace('alias', '`' . $alias->base . '`', $join);

            $joinCondition .= ' AND ' . $join;
        }

        foreach ( $this->setQueryRestriction($relationship->reference_model, $alias->base) as $item )
            $joinCondition .= ' AND ' . $item;

        $this->tables[ $alias->base ] = [
            'table' => $alias->table,
            'join'  => $joinCondition,
        ];
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
            if ( !$item->is_custom_column )
                $columns[ $this->base . '.' . $item->name ] = '`' . $this->base . '`.' . $item->name;
        }

        //add only those columns which are permitted for the user
        foreach ( $this->layout as $item ) {
            $name = $item['object'] . '.' . $item['column'];
            if ( in_array($name, $this->acceptedColumns) )
                $columns[ $name ] = '`' . $item['object'] . '`.' . $item['column'];
            else
                Message::info('Requested column ' . $name . ' cannot be served');
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
        $this->encryptedColumns = $record->sql['encrypted_columns'];
        $this->sourceColumns = $record->sql['source_columns'];

        return true;
    }

    /**
     * Create the sql join against the tables that are attached as part of the inclusions
     * This is part of the where condition
     * @return mixed|string
     */
    private function getJoins () {
        $query = '';
        foreach ( $this->restrictions as $join ) {
            if ( !$join ) continue;

            if ( $query )
                $query .= ' AND ' . $join;
            else
                $query = $join;
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
            if ( $query )
                $query .= ' LEFT JOIN ' . $value['table'] . ' `' . $key . '`';
            else
                $query = $value['table'] . ' `' . $key . '`';

            if ( $value['join'] )
                $query .= ' ON ' . $value['join'];
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
        $this->sql['encrypted_columns'] = $this->encryptedColumns;
        $this->sql['source_columns'] = $this->sourceColumns;

        $this->sqlCacheIdentifier = md5($this->model->model_hash . '-' . microtime('true') . '-' . md5($this->includes));
        Cache::put($this->sqlCacheIdentifier, (object) [
            'user_id' => Auth::id(),
            'sql'     => $this->sql,
            'time'    => strtotime('now'),
        ], 30);
    }

    /**
     * @param $base
     * @param $model
     */
    protected function setReadDictionary ($base, $model) {
        $columns = ModelManager::getModelDictionary($model, 'r');
        $this->dictionary[ $base ] = $columns->allowed;

        foreach ( $columns->restrictedIdentifiers as $item )
            array_push($this->rejectedColumns, $base . '.' . $item);

        foreach ( $columns->allowedIdentifiers as $item ) {
            array_push($this->acceptedColumns, $base . '.' . $item);

            //find all encrypted columns and act decrypt them before sending data to the user
            if ( in_array($item, $columns->encryptedColumns) )
                array_push($this->encryptedColumns, $base . '.' . $item);

            //find all the source columns and then change the source
            // values before sending to the end user
            if ( in_array($item, $columns->sourceColumns) )
                array_push($this->sourceColumns, $base . '.' . $item);
        }
    }

    /**
     * @param $model
     * @param $base
     * @return array
     */
    private function setQueryRestriction ($model, $base) {
        $joins = [];

        $restrictions = BusinessRuleManager::getQueryStrings($model);
        foreach ( $restrictions as $restriction ) {
            $restriction = str_replace('current', '`' . $base . '`', $restriction);

            //add all current implementation of restrictions
            array_push($joins, '(' . $restriction . ')');
        }

        return $joins;
    }
}
