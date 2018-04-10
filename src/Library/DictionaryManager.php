<?php

namespace Drivezy\LaravelRecordManager\Library;


use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Drivezy\LaravelUtility\Models\BaseModel;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DictionaryManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class DictionaryManager {

    /**
     * @var DataModel|null
     */
    private $model = null;
    /**
     * @var null
     */
    private $columnMappings = null;

    /**
     * @var array
     */
    private $userColumns = [
        'created_by',
        'updated_by',
    ];

    /**
     * @var array
     */
    private $allowedRelationshipParents = [
        BaseModel::class,
    ];

    /**
     * @var array
     */
    private $userRelationshipNames = ['created_user', 'updated_user'];

    /**
     * DictionaryManager constructor.
     * @param DataModel $model
     */
    public function __construct (DataModel $model) {
        $this->model = $model;

    }

    /**
     *
     */
    public function process () {
        self::loadModelColumns();
        self::loadModelMethods();
    }


    /**
     *
     */
    private function loadModelColumns () {
        $this->loadColumnMappings();

        $className = $this->model->namespace . '\\' . $this->model->name;
        $model = new $className();

        $schema = $model->getConnection()->getSchemaBuilder();
        $columns = $schema->getColumnListing($model->getTable());

        foreach ( $columns as $column ) {
            if ( in_array($column, $this->userColumns) ) {
                self::attachModelColumn($column, '', [
                    'reference_model_id' => 1,
                    'column_type_id'     => 6,
                ]);
                continue;
            }

            if ( substr($column, -3) == '_id' )
                self::attachModelColumn($column, '', [
                    'column_type_id' => 6,
                ]);
            else
                self::attachModelColumn($column, $schema->getColumnType($model->getTable(), $column));
        }
    }

    /**
     *
     */
    private function loadModelMethods () {
        $className = $this->model->namespace . '\\' . $this->model->name;
        array_push($this->allowedRelationshipParents, $className);

        $class = new \ReflectionClass($className);

        $methods = $class->getMethods();
        foreach ( $methods as $method ) {
            if ( in_array($method->name, $this->userRelationshipNames) ) {
                self::attachModelRelationship($method->name, [
                    'reference_model_id' => 1,
                ]);
                continue;
            }

            if ( in_array($method->class, $this->allowedRelationshipParents) && !$method->isStatic() )
                self::attachModelRelationship($method->name);
        }
    }

    /**
     * @param $column
     * @param $type
     * @param array $arr
     * @return mixed
     */
    private function attachModelColumn ($column, $type, $arr = []) {
        $record = ModelColumn::firstOrNew([
            'name'     => $column,
            'model_id' => $this->model->id,
        ]);
        if ( $record->id ) return $record;

        $record->display_name = ucwords(str_replace('_', ' ', str_replace('_id', '', $column)));
        $record->column_type_id = self::getColumnMapping($type);

        foreach ( $arr as $key => $value )
            $record->{$key} = $value;

        $record->save();

        return $record;
    }

    /**
     * @param $method
     * @param array $arr
     * @return mixed
     */
    private function attachModelRelationship ($method, $arr = []) {
        $record = ModelRelationship::firstOrNew([
            'name'     => $method,
            'model_id' => $this->model->id,
        ]);
        if ( $record->id ) return $record;

        $record->display_name = ucwords(str_replace('_', ' ', $method));
        $record->column_id = self::getModelMethodColumn($method);
        $record->reference_type_id = self::getMethodRelationshipType($method);

        foreach ( $arr as $key => $value )
            $record->{$key} = $value;

        $record->save();

        return $record;
    }

    /**
     *
     */
    private function loadColumnMappings () {
        $records = LookupValue::where('lookup_type_id', 1)->get();
        foreach ( $records as $record ) {
            $items = explode(',', $record->value);
            foreach ( $items as $item )
                $this->columnMappings[ strtolower($item) ] = $record->id;
        }

    }

    /**
     * @param $type
     * @return null
     */
    private function getColumnMapping ($type) {
        return isset($this->columnMappings[ $type ]) ? $this->columnMappings[ $type ] : null;
    }

    /**
     * @param $method
     * @return null
     */
    private function getModelMethodColumn ($method) {
        $record = ModelColumn::where('model_id', $this->model->id)->where('name', $method . '_id')->first();
        if ( $record ) return $record->id;

        $record = ModelColumn::where('model_id', $this->model->id)->where('name', strtolower(preg_replace('/[A-Z]/', '_$0', $method)))->first();
        if ( $record ) return $record->id;

        return null;
    }

    /**
     * @param $method
     * @return int
     */
    private function getMethodRelationshipType ($method) {
        if ( substr($method, 0, 5) == 'scope' ) return 23;

        if ( substr($method, -1) == 's' ) return 22;

        return 21;
    }


}