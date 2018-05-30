<?php

namespace Drivezy\LaravelRecordManager\Library;


use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Drivezy\LaravelUtility\Models\LookupValue;

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

        $class = new \ReflectionClass($className);
        $methods = $class->getMethods();

        foreach ( $methods as $method ) {
            if ( in_array($method->name, $this->userRelationshipNames) ) {
                self::attachModelRelationship($method->name, [
                    'reference_model_id' => 1,
                ]);
                continue;
            }

            if ( $method->class == $className && !$method->isStatic() ) {
                self::attachModelRelationship($method->name);
            }
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
        $record->source_column_id = self::getModelMethodColumn($method);
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
        $record = ModelColumn::where('model_id', $this->model->id)->where('name', strtolower($method) . '_id')->first();
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
        if ( substr($method, 0, 5) == 'scope' ) return 3;

        if ( substr($method, -1) == 's' ) return 2;

        return 1;
    }

    /**
     * @param $className
     * @param $includes
     * @return array
     */
    public static function getModelDictionary ($className, $includes) {
        $models = [];

        $splits = explode('\\', $className);
        $base = end($splits);

        array_push($models, $base);

        //fetching model against the base
        $model = DataModel::where('model_hash', md5($className))->first();
        if ( !$model ) return null;

        $modelId = $model->id;
        $dictionary = $links = [];

        $links[ strtolower($base) ] = $model;
        $dictionary[ strtolower($base) ] = self::getModelColumns($modelId, true);

        foreach ( $includes as $include ) {
            $splits = explode('.', $include);
            $relatedId = $modelId;
            $relationShipName = strtolower($base);

            foreach ( $splits as $split ) {
                $split = trim($split);
                $relationShipName .= '.' . strtolower($split);
                $alias = self::getModelAlias($relatedId, $split);

                if ( $alias && $alias->reference_model ) {
                    $relatedId = $alias->reference_model_id;

                    $dictionary[ $relationShipName ] = self::getModelColumns($relatedId, true);
                    $alias->actions = ModelManager::getModelActions($alias->reference_model);

//                    $alias->actions = self::getDistinctActions('ModelAlias', $alias->id, $alias->related_model);
//                    $alias->preferences = self::getUserPreferences($relationShipName);

                    $links[ $relationShipName ] = $alias;
                }
            }
        }

        return [$dictionary, $links];
    }

    /**
     * @param $modelId
     * @param bool $relatedModel
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private static function getModelColumns ($modelId, $relatedModel = false) {
        $includes = $relatedModel ? ['reference_model'] : [];

        return ModelColumn::with($includes)->where('model_id', $modelId)->where('visibility', true)->get();
    }

    /**
     * @param $modelId
     * @param $alias
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    private static function getModelAlias ($modelId, $alias) {
        return ModelRelationship::with(array('reference_type', 'reference_model'))->where('model_id', $modelId)->where('name', $alias)->first();
    }

}