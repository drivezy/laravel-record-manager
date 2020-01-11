<?php

namespace Drivezy\LaravelRecordManager\Library;


use Drivezy\LaravelAdmin\Library\UIActionManager;
use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Message;

class RecordManager extends DataManager
{
    private $detailArray = [];
    private $recordData = null;

    /**
     * return the data as part of the record with its tabs
     * @param $id
     * @return array
     */
    public function process ($id = null)
    {
        $className = $this->model->namespace . '\\' . $this->model->name;
        $this->recordData = $className::find($id);

        if ( !self::loadDataFromCache() ) {
            parent::process();

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
                'model_class'        => $className,
                'model_hash'         => md5($className),
            ],
            'tabs'   => $this->detailArray,
        ];
    }

    /**
     * Differentiate the multiple includes to single and tabs and create data accordingly
     * @return bool
     */
    private function segregateIncludes ()
    {
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
                if ( !$data ) {
                    Message::info('Relationship ' . $relationship . ' not found');
                    break;
                };

                //user does not have access to the model
                if ( !ModelManager::validateModelAccess($data->reference_model, ModelManager::READ) ) {
                    $message = 'Access to reference model ' . $base . '.' . $relationship . ' : ' . $data->reference_model_id;
                    $message .= $data->reference_model ? ' is prohibited ' : ' is not found';
                    Message::warn($message);

                    break;
                }


                if ( $first && $data->reference_type_id == 42 ) {
                    if ( !isset($this->detailArray[ $relationship ]) ) {
                        $sourceColumn = $data->source_column_id ? $data->source_column->name : 'id';
                        $aliasColumn = $data->alias_column_id ? $data->alias_column->name : 'id';

                        //checking the join condition with the string or numeric and accordingly setting up the alias
                        $sourceColumnData = is_numeric($this->recordData->{$sourceColumn}) ? $this->recordData->{$sourceColumn} : '\'' . $this->recordData->{$sourceColumn} . '\'';
                        $restrictedQuery = '`' . strtolower($data->reference_model->name) . '`.' . $aliasColumn . ' = ' . $sourceColumnData;

                        //added restricted query for join condition
                        if ( $data->join_definition ) {
                            $join = str_replace('alias', '`' . strtolower($data->reference_model->name) . '`', $data->join_definition);
                            $restrictedQuery .= ' and ' . $join;
                        }

                        $this->detailArray[ $relationship ] = [
                            'id'                => $data->id,
                            'default_ordering'  => $data->default_ordering,
                            'base'              => strtolower($data->reference_model->name),
                            'name'              => $data->display_name,
                            'includes'          => [],
                            'restricted_query'  => $restrictedQuery,
                            'restricted_column' => $aliasColumn,
                            'route'             => $data->reference_model->route_name,
                            'list_layouts'      => PreferenceManager::getListPreference(md5(ModelRelationship::class), $data->id),
                            'form_layouts'      => PreferenceManager::getFormPreference(md5(ModelRelationship::class), $data->id),
                            'ui_actions'        => UIActionManager::getObjectUIActions(md5(ModelRelationship::class), $data->id),
                            'model_class'       => $data->reference_model->namespace . '\\' . $data->reference_model->name,
                            'model_hash'        => $data->reference_model->model_hash,
                            'alias_model_id'    => $data->reference_model_id,
                        ];
                    }

                    array_push($this->detailArray[ $relationship ]['includes'], str_replace($relationship . '.', '', $include));
                    break;
                }


                if ( !$first && $data->reference_type_id != 41 ) break;

                //set up the joins against the necessary columns
                self::setupColumnJoins($model, $data, $base);

                //setting up the required documents
                $base .= '.' . $relationship;
                $model = $data->reference_model;
                $first = false;


                $this->relationships[ $base ] = $data;
                $this->dictionary[ $base ] = Column::where('source_type', md5(DataModel::class))->where('source_id', $data->reference_model_id)->get();
                $this->setReadDictionary($base, $model);
            }
        }
    }

    /**
     * Load the results of the record as requested by the record condition
     */
    private function loadResults ()
    {
        $sql = 'SELECT ' . $this->sql['columns'] . ' FROM ' . $this->sql['tables'] . ' WHERE ' . $this->sql['joins'] . ' AND `' . $this->base . '`.id = ' . $this->recordData->id;
        $this->data = DB::select(DB::raw($sql))[0];

        //adding check for encrypted column. if decryption fails then set to some logical value
        foreach ( $this->encryptedColumns as $item ) {
            try {
                $this->data->{$item} = Crypt::decrypt($this->data->{$item});
            } catch ( \Exception $e ) {
                $this->data->{$item} = 'Invalid Account Number';
            }
        }

        //post process the source record
        foreach ( $this->sourceColumns as $column ) {
            $sourceId = str_replace(last(explode('.', $column)), explode('_', last(explode('.', $column)))[0] . '_id', $column);
            $sourceRecord = RecordManagement::getSourceColumnValue($this->data->{$column}, $this->data->{$sourceId});

            $this->data->{$column} = 'Object-' . $sourceRecord[0];
            $this->data->{$sourceId} = 'Record-' . $sourceRecord[1];
        }
    }
}
