<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelRelationship;
use Drivezy\LaravelUtility\Models\BaseModel;
use Illuminate\Http\Request;
use JRApp\Libraries\Utility\RecordManagement;

/**
 * Class ApiResponseManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class ApiResponseManager {

    private $model = null;
    private $dataModel = null;
    /**
     * @var Request|null
     */
    private $request = null;

    /**
     * ApiResponseManager constructor.
     * @param Request $request
     * @param $model
     */
    public function __construct (Request $request, $model) {
        $this->model = $model;
        RecordManagement::$model = $model;
        $this->dataModel = DataModel::where('model_hash', md5($this->model))->first();

        $this->request = $request;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index () {

        return fixed_response(RecordManagement::index($this->request));

        $model = $this->model;

        $query = $this->getEncodedQuery();
        $query = $model::whereRaw($query['query'], $query['value']);

        $data = $this->getRecordData($query);

        $data['success'] = true;

        return $data;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show ($id) {
        return RecordManagement::show($id);

        $model = $this->model;
        $includes = $this->getQueryInclusions();

        return success_response($model::with($includes)->find($id));
    }

    /**
     * @return array
     */
    private function getEncodedQuery () {
        $query = $this->request->get('query');
        if ( !$this->request->has('query') ) return array('query' => '1 < ?', 'value' => array('2'));

        $splits = explode(',', $query);
        $encode = $arr = [];
        $flag = true;

        foreach ( $splits as $split ) {
            if ( $flag ) {
                $encode['query'] = $split;
                $flag = false;
            } else
                array_push($arr, $split);
        }
        $encode['value'] = $arr;

        return $encode;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function addQueryParams ($query) {
        $request = $this->request;

        if ( $request->has('scopes') ) {
            $scopes = explode(',', $request->get('scopes'));
            foreach ( $scopes as $scope ) {
                $query->{$scope}();
            }
        }

        if ( $request->has('in') ) {
            $ins = explode("and", $request->get('in'));
            foreach ( $ins as $in ) {
                $x = explode('=', $in);
                $query->whereIn(trim($x[0]), explode(',', trim($x[1])));
            }
        }

        if ( $request->has('not_in') ) {
            $ins = explode("and", $request->get('not_in'));
            foreach ( $ins as $in ) {
                $x = explode('=', $in);
                $query->whereNotIn(trim($x[0]), explode(',', trim($x[1])));
            }
        }

        return $query;
    }

    /**
     * @param $query
     * @return array|mixed
     */
    public function getRecordData ($query) {
        $request = $this->request;

        $response = [];
        $query = $this->addQueryParams($query);

        if ( $request->has('aggregation_column') )
            return self::handleAggregation($query);

        $includes = self::getQueryInclusions();
        $limit = $request->has('limit') ? intval($request->get('limit')) : 20;

        $offset = $request->has('page') ? ( $request->get('page') - 1 ) * $limit : 0;
        $offset = $offset > 0 ? $offset : 0;

        if ( $request->has('order') ) {
            $splits = explode(',', $request->get('order'));
            $order = $request->has('order') ? $splits[0] : 'id';
            $orderingOrder = isset($splits[1]) ? $splits[1] : 'ASC';
        } else {
            $order = 'id';
            $orderingOrder = 'ASC';
        }

        if ( $request->has('stats') ) {
            if ( $request->get('stats') == 'true' ) {
                $count = $query->count();
                $stats = array('records' => $count, 'count' => $limit, 'offset' => $offset);
                $response['stats'] = $stats;
            }
        }

        $data = $query->with($includes)
            ->skip($offset)
            ->limit($limit)
            ->orderBy($order, $orderingOrder)
            ->get();

        $response['response'] = $data;

        return $response;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function handleAggregation ($query) {
        $request = $this->request;
        $response['response'] = $query->{$request->get('aggregation_operator')}($request->get('aggregation_column'));

        return $response;
    }

    /**
     * @return array
     */
    private function getQueryInclusions () {
        $includes = $this->request->get('includes');
        if ( !$includes ) return [];

        if ( $includes == 'null' ) return [];

        $includes = explode(',', $includes);
        $actedModels = $allowedIncludes = [];

        foreach ( $includes as $include ) {
            $inclusion = null;
            $aliasId = $this->dataModel->id;

            //split the inclusions
            $splits = explode('.', $include);

            foreach ( $splits as $split ) {
                $alias = ModelRelationship::with('reference_model')->where('name', strtolower($split))
                    ->where('model_id', $aliasId)->first();

                //if the given alias has no binding in db or the reference model is missing
                if ( !( $alias && $alias->reference_model_id ) ) break;

                if ( !ModelManager::validateModelAccess($alias->reference_model, ModelManager::READ) )
                    break;

                //get the details against the relationship model
                $class = $alias->reference_model->namespace . '\\' . $alias->reference_model->name;
                $inclusion = $inclusion ? $inclusion . '.' . $split : $split;
                $aliasId = $alias->id;

                //check if validation against the model has been done or not
                if ( in_array($class, $actedModels) ) continue;
                array_push($actedModels, $class);

                //hide columns which are restricted due to security rule
                $columns = ModelManager::getModelDictionary($alias->reference_model, ModelManager::READ);
                BaseModel::$hide_columns[ $class ] = $columns ? $columns->restrictedIdentifiers : [];
            }

            //add to inclusion only when it is valid
            if ( $inclusion )
                array_push($allowedIncludes, $inclusion);
        }

        return $allowedIncludes;
    }


}