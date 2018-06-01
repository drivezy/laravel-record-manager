<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelAccessManager\AccessManager;
use Drivezy\LaravelRecordManager\Library\DictionaryManager;
use Drivezy\LaravelRecordManager\Library\ListManager;
use Drivezy\LaravelRecordManager\Library\ModelManager;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ListPreference;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

/**
 * Class RecordManager
 * @package Drivezy\LaravelRecordManager\Controller
 */
class BaseController extends Controller {
    /**
     * @var
     */
    public $model;

    /**
     * @var null
     */
    public $request = null;

    public function __construct () {
        $this->model = DataModel::where('model_hash', md5($this->model))->first();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index (Request $request) {
        if ( !ModelManager::validateModelAccess($this->model, ModelManager::READ) )
            return AccessManager::unauthorizedAccess();

        if ( $request->has('list') )
            return self::getListIndex($request);

        $this->request = $request;
        $model = $this->model;

        $query = $this->getEncodedQuery();
        $query = $model::whereRaw($query['query'], $query['value']);

        $data = $this->getRecordData($query);
        $data['success'] = true;

        return $data;
    }

    private function getListIndex (Request $request) {
        $listPreference = [];
        if ( $request->has('layout_id') ) {
            $listPreference = ListPreference::find($request->layout_id);
            $listPreference = $listPreference->column_definition;
        }
        $records = ( new ListManager($this->model, [
            'includes'           => $request->has('includes') ? $request->get('includes') : false,
            'layout'             => $listPreference,
            'stats'              => $request->has('stats') ? $request->get('stats') : false,
            'query'              => $request->has('query') ? $request->get('query') : false,
            'sqlCacheIdentifier' => $request->has('request_identifier') ? $request->get('request_identifier') : false,
            'limit'              => $request->has('limit') ? $request->get('limit') : 20,
            'page'               => $request->has('page') ? $request->get('page') : 1,
        ]) )->process();

        return Response::json(['success' => true, 'response' => $records]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show (Request $request, $id) {
        if ( !ModelManager::validateModelAccess($this->model, ModelManager::READ) )
            return AccessManager::unauthorizedAccess();

        if ( !is_numeric($id) )
            return Response::json(['success' => false, 'response' => 'invalid operation']);

        $this->request = $request;
        $model = $this->model;

        $includes = $this->getQueryInclusions();
        $response['response'] = $model::with($includes)->find($id);
        $response['success'] = true;

        return Response::json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store (Request $request) {
        if ( !ModelManager::validateModelAccess($this->model, ModelManager::ADD) )
            return AccessManager::unauthorizedAccess();

        $model = $this->model;
        $data = $model::create($request->except('access_token'));

        if ( !isset($data->errors) )
            return Response::json(['success' => true, 'response' => $data]);

        return Response::json(['success' => false, 'response' => $data, 'reason' => 'Validation error']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return null
     */
    public function update (Request $request, $id) {
        if ( !ModelManager::validateModelAccess($this->model, ModelManager::EDIT) )
            return AccessManager::unauthorizedAccess();

        if ( !is_numeric($id) )
            return Response::json(['success' => false, 'response' => 'invalid operation']);

        $model = $this->model;

        $data = $model::find($id);
        if ( !$data ) return null;

        $inputs = $request->except('deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by', 'access_token');

        foreach ( $inputs as $key => $value )
            $data->setAttribute($key, $this->convertToDbValue($value));

        $data->save();

        if ( !isset($data->errors) )
            return Response::json(['success' => true, 'response' => $data]);

        return Response::json(['success' => false, 'response' => $data, 'reason' => 'Validation error']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy ($id) {
        if ( !ModelManager::validateModelAccess($this->model, ModelManager::DELETE) )
            return AccessManager::unauthorizedAccess();

        $model = $this->model;

        $data = $model::find($id);
        if ( !$data )
            return Response::json(['success' => true, 'response' => null]);

        $data->delete();

        return Response::json(['success' => true, 'response' => $data]);
    }

    /**
     * @return array
     */
    private function getEncodedQuery () {
        $query = $this->request->get('query');
        if ( !$query ) return array('query' => '1 < ?', 'value' => array('2'));

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

        if ( $request->has('dictionary') ) {
            if ( $request->get('dictionary') == 'true' ) {
                $dictionary = DictionaryManager::getModelDictionary($this->model, $includes);
                $response['dictionary'] = $dictionary[0];
                $response['relationship'] = $dictionary[1];
            }
        }

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

        return explode(',', $includes);
    }

    /**
     * Convert the data into the corresponding db value. Handling null and empty as well as 0 & false ones
     * @param $value
     * @return int|null
     */
    private function convertToDbValue ($value) {
        if ( is_null($value) ) {
            $val = null;
        } elseif ( $value === 0 || $value === "0" || $value === false || $value === "false" ) {
            $val = 0;
        } elseif ( empty($value) ) {
            $val = null;
        } else
            $val = $value;

        return $val;
    }
}