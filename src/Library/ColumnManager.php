<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;

class ColumnManager {

    private $rules = [];
    private $data = null;

    public $columns = [];

    public $restricted = [];
    public $restrictedIdentifiers = [];

    public $allowed = [];
    public $allowedIdentifiers = [];

    public $encryptedColumns = [];
    public $sourceColumns = [];

    public function __construct ($type, $id, $obj = []) {
        $this->source_type = $type;
        $this->source_id = $id;

        foreach ( $obj as $key => $value )
            $this->{$key} = $value;

        $this->process();
    }

    /**
     *
     */
    public function process () {
        $this->columns = $this->getDictionary();

        foreach ( $this->columns as $column ) {
            //check if there are any security rule against the column
            $rules = isset($this->rules[ $column->name ]) ? $this->rules[ $column->name ] : [];

            //segregate between approved v/s unapproved columns
            if ( SecurityRuleManager::evaluateSecurityRules($rules, $this->data) ) {
                array_push($this->allowed, $column);
                array_push($this->allowedIdentifiers, $column->name);

                if ( $column->column_type_id == 18 )
                    array_push($this->encryptedColumns, $column->name);

                if ( $column->column_type_id == 20 ) {
                    array_push($this->sourceColumns, $column->name);
                }


            } else {
                array_push($this->restricted, $column);
                array_push($this->restrictedIdentifiers, $column->name);
            }
        }
    }

    /**
     * @return Column[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getDictionary () {
        return Column::with(['reference_model'])->where('source_type', $this->source_type)
            ->where('source_id', $this->source_id)
            ->get();
    }

    /**
     * get the records for which source columns are aligned to
     * @param $source model hash against a given model
     * @param $sourceId
     * @return object
     */
    public static function getSourceColumnDetails ($source, $sourceId) {
        //find the model against which record is to be found out
        $source = DataModel::where('model_hash', $source)->first();
        $source_id = $sourceId;

        //if source is present, then go with the related record
        if ( $source ) {
            $model = $source->namespace . '\\' . $source->name;
            $source_id = $model::find($source_id);
        }

        //return array of record for source columns
        return (object) [
            'source_type' => $source,
            'source_id'   => $source_id,
        ];
    }
}
