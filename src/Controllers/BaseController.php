<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelAdmin\Library\ClientScriptManager;
use Drivezy\LaravelRecordManager\Library\AdminResponseManager;
use Drivezy\LaravelRecordManager\Library\ApiResponseManager;
use Drivezy\LaravelRecordManager\Library\ModelManager;
use Drivezy\LaravelRecordManager\Library\PreferenceManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class RecordManager
 * @package Drivezy\LaravelRecordManager\Controller
 */
class BaseController extends Controller {
    protected $model;
    private $dataModel;
    private $request = null;

    /**
     * BaseController constructor.
     */
    public function __construct () {
        $this->dataModel = DataModel::where('model_hash', md5($this->model))->first();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index (Request $request) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::READ) )
            return AccessManager::unauthorizedAccess();

        if ( $request->has('list') )
            return ( new AdminResponseManager($request, $this->dataModel) )->index();

        return ( new ApiResponseManager($request, $this->model) )->index();
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show (Request $request, $id) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::READ) )
            return AccessManager::unauthorizedAccess();

        if ( !is_numeric($id) )
            return invalid_operation();

        if ( $request->has('list') )
            return ( new AdminResponseManager($request, $this->dataModel) )->show($id);

        return ( new ApiResponseManager($request, $this->model) )->show($id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create (Request $request) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::ADD) )
            return AccessManager::unauthorizedAccess();

        $columns = ModelManager::getModelDictionary($this->dataModel, ModelManager::ADD);

        return success_response([
            'dictionary'     => [
                strtolower($this->dataModel->name) => $columns->allowed,
            ],
            'form_layouts'   => PreferenceManager::getFormPreference(md5(DataModel::class), $this->dataModel->id),
            'model'          => $this->dataModel,
            'client_scripts' => ClientScriptManager::getClientScripts($this->dataModel->table_name),
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::ADD) )
            return AccessManager::unauthorizedAccess();

        $model = $this->model;

        $columns = ModelManager::getModelDictionary($this->dataModel, ModelManager::ADD);
        $inputs = $request->except('deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by', 'access_token');

        $data = new $model();
        foreach ( $inputs as $key => $value ) {
            if ( in_array($key, $columns->restrictedIdentifiers) ) continue;

            $data->setAttribute($key, $this->convertToDbValue($value));
        }

        $data->save();

        if ( !( isset($data->errors) || $data->abort ) )
            return success_response($data);

        return failure_message($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function edit (Request $request, $id) {
        $model = $this->model;
        $data = $model::find($id);

        $columns = ModelManager::getModelDictionary($this->dataModel, ModelManager::EDIT, $data);
        foreach ( $columns->restrictedIdentifiers as $item ) {
            unset($data->{$item});
        }

        return success_response([
            'data'           => $data,
            'dictionary'     => [
                strtolower($this->dataModel->name) => $columns->allowed,
            ],
            'form_layouts'   => PreferenceManager::getFormPreference(md5(DataModel::class), $this->dataModel->id),
            'model'          => $this->dataModel,
            'client_scripts' => ClientScriptManager::getClientScripts($this->dataModel->table_name),
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return null
     */
    public function update (Request $request, $id) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::EDIT) )
            return AccessManager::unauthorizedAccess();

        if ( !is_numeric($id) )
            return invalid_operation();

        $model = $this->model;

        $data = $model::find($id);
        if ( !$data ) return null;

        $inputs = $request->except('deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by', 'access_token');
        $columns = ModelManager::getModelDictionary($this->dataModel, ModelManager::EDIT, $data);

        foreach ( $inputs as $key => $value ) {
            if ( in_array($key, $columns->restrictedIdentifiers) ) continue;

            $data->setAttribute($key, $this->convertToDbValue($value));
        }

        $data->save();

        if ( !( isset($data->errors) || $data->abort ) )
            return success_response($data);

        return failure_message($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy (Request $request, $id) {
        if ( !ModelManager::validateModelAccess($this->dataModel, ModelManager::DELETE) )
            return AccessManager::unauthorizedAccess();

        $model = $this->model;

        $data = $model::find($id);
        if ( !$data )
            return failed_response('Record not found');

        $data->delete();

        if ( !( isset($data->errors) || $data->abort ) )
            return success_response($data);

        return failure_message($data);
    }

    /**
     * Convert the data into the corresponding db value. Handling null and empty as well as 0 & false ones
     * @param $value
     * @return int|null
     */
    private function convertToDbValue ($value) {
        if ( is_null($value) ) {
            $val = null;
        } elseif ( $value === 0 || $value === "0" || $value === false || $value === "false" || $value === 0.0 ) {
            $val = 0;
        } elseif ( empty($value) ) {
            $val = null;
        } else
            $val = $value;

        return $val;
    }
}