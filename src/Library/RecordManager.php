<?php

namespace Drivezy\LaravelRecordManager\Library;


use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Illuminate\Support\Facades\DB;

class RecordManager extends DataManager {
    private $detailArray = [];
    private $recordData = null;

    /**
     * @param $id
     * @return array
     */
    public function process ($id) {
        $className = $this->model->namespace . '\\' . $this->model->name;
        $this->recordData = $className::find($id);

        if ( !self::loadDataFromCache() ) {
            self::segregateIncludes();
            self::constructQuery();
        }

        self::loadResults();

        return [
            'record' => [
                'base'               => $this->base,
                'data'               => $this->data,
                'relationship'       => $this->relationships,
                'dictionary'         => $this->dictionary,
                'request_identifier' => $this->sqlCacheIdentifier,
            ],
            'tabs'   => $this->detailArray,
        ];
    }

    /**
     * @return bool
     */
    private function segregateIncludes () {
        if ( !$this->includes ) return true;

        $includes = explode(',', $this->includes);
        foreach ( $includes as $include ) {
            $relationships = explode('.', $include);

            $model = $this->model;
            $base = $this->base;

            $first = true;

            foreach ( $relationships as $relationship ) {
                $data = ModelRelationship::with(['reference_model', 'source_column', 'alias_column'])
                    ->where('model_id', $model->id)->where('name', $relationship)
                    ->first();

                //relationship against that item is not found
                if ( !$data ) break;

                //user does not have access to the model
                if ( !ModelManager::validateModelAccess($data->reference_model, ModelManager::READ) )
                    break;

                if ( $first && $data->reference_type_id == 2 ) {
                    if ( !isset($this->detailArray[ $relationship ]) ) {
                        $sourceColumn = $data->source_column_id ? $data->source_column->name : 'id';
                        $aliasColumn = $data->alias_column_id ? $data->alias_column->name : 'id';

                        $this->detailArray[ $relationship ] = [
                            'id'                => $data->id,
                            'base'              => strtolower($data->reference_model->name),
                            'display_name'      => $data->display_name,
                            'includes'          => [],
                            'query'             => '`' . strtolower($data->reference_model->name) . '`.' . $aliasColumn . ' = ' . $this->recordData->{$sourceColumn},
                            'restricted_column' => $aliasColumn,
                            'route'             => $data->reference_model->route_name,
                            'list_layouts'      => PreferenceManager::getListPreference(ModelRelationship::class, $data->id),
                            'form_layouts'      => PreferenceManager::getFormPreference(ModelRelationship::class, $data->id),
                        ];
                    }

                    array_push($this->detailArray[ $relationship ]['includes'], str_replace($relationship . '.', '', $include));
                    break;
                }

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
        $sql = 'SELECT ' . $this->sql['columns'] . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'] . ' AND `' . $this->base . '`.id = ' . $this->recordData->id;
        $this->data = DB::select(DB::raw($sql))[0];
    }
}