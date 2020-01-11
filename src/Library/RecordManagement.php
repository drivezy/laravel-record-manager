<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\Library\EventQueueManager;
use Illuminate\Http\Request as Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class RecordManagement
 * @package JRApp\Libraries
 */
class RecordManagement
{
    public static $model;
    public static $dictionary = null;

    /**
     * @param $request
     * @return array
     */
    public static function index ($request)
    {
        $model = self::$model;

        if ( $request->has('export') && $request->get('export') ) {
            return self::setRequestForExport($request);
        }

        $query = self::getEncodedQuery($request->get('query'));
        $query = $model::whereRaw($query['query'], $query['value']);

        $data = self::getRecordData($query);
        $data['success'] = true;

        return $data;
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public static function store (Request $request)
    {
        $model = self::$model;
        $data = $model::create($request->except('access_token'));

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function show ($id)
    {
        $model = self::$model;

        $includes = self::getQueryInclusions();
        $response['response'] = $model::with($includes)->find($id);
        $response['success'] = true;

        return $response;
    }


    /**
     * @param Request $request
     * @param $id
     * @return null
     */
    public static function update (Request $request, $id)
    {
        $model = self::$model;

        $data = $model::find($id);
        if ( !$data ) return null;

        $inputs = $request->except('deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by', 'access_token');

        foreach ( $inputs as $key => $value )
            $data->setAttribute($key, self::convertToDbValue($value));

        $data->save();

        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function destroy ($id)
    {
        $model = self::$model;

        $data = $model::find($id);
        if ( !$data )
            return null;
        $data->delete();

        return $data;
    }

    /**
     * @param $query
     * @return array
     */
    public static function getEncodedQuery ($query)
    {
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
     * @param $formQuery
     * @return array
     */
    public static function getRecordDataWithQuery ($query, $formQuery)
    {
        $encodedQuery = self::getEncodedQuery($formQuery);
        $query = $query->whereRaw($encodedQuery['query'], $encodedQuery['value']);

        return self::getRecordData($query);
    }

    /**
     * @param $query
     * @return array
     */
    public static function getRecordData ($query)
    {
        $response = [];
        $query = self::addQueryParams($query);

        if ( \Illuminate\Support\Facades\Request::has('aggregation_column') )
            return self::handleAggregation($query);

        $includes = self::getQueryInclusions();
        $limit = \Illuminate\Support\Facades\Request::has('limit') ? intval(\Illuminate\Support\Facades\Request::get('limit')) : 20;

        $offset = \Illuminate\Support\Facades\Request::has('offset') ? intval(\Illuminate\Support\Facades\Request::get('offset')) : 0;
        $offset = \Illuminate\Support\Facades\Request::has('page') ? ( \Illuminate\Support\Facades\Request::get('page') - 1 ) * $limit : $offset;
        $offset = $offset > 0 ? $offset : 0;

        if ( \Illuminate\Support\Facades\Request::has('order') ) {
            $splits = explode(',', \Illuminate\Support\Facades\Request::get('order'));
            $order = \Illuminate\Support\Facades\Request::has('order') ? $splits[0] : 'id';
            $orderingOrder = isset($splits[1]) ? $splits[1] : 'ASC';
        } else {
            $order = 'id';
            $orderingOrder = 'ASC';
        }

        if ( \Illuminate\Support\Facades\Request::has('stats') ) {
            if ( \Illuminate\Support\Facades\Request::get('stats') == 'true' ) {
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
     * @return mixed
     */
    private static function getDictionaryStarter ()
    {
        $splits = explode('\\', self::$model);

        return end($splits);
    }

    /**
     * @param $request
     * @return array
     */
    private static function setRequestForExport ($request)
    {
        $data['payload'] = $request->only('query', 'in', 'not_in', 'export_columns', 'export_comment', 'export_name', 'order');
        $data['user_id'] = Auth::id();
        $data['model'] = self::$model;


        return ["success" => true, "response" => EventQueueManager::setEvent('export.query.data', serialize($data))];
    }

    /**
     * Convert the data into the corresponding db value. Handling null and empty as well as 0 & false ones
     * @param $value
     * @return int|null
     */
    public static function convertToDbValue ($value)
    {
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

    /**
     * @return array
     */
    private static function getQueryInclusions ()
    {
        $includes = \Illuminate\Support\Facades\Request::get('includes');
        if ( !$includes ) return [];

        if ( $includes == 'null' ) return [];

        return explode(',', $includes);
    }

    /**
     * @param $query
     * @return mixed
     */
    private static function addQueryParams ($query)
    {
        if ( \Illuminate\Support\Facades\Request::has('scopes') ) {
            $scopes = explode(',', \Illuminate\Support\Facades\Request::get('scopes'));
            foreach ( $scopes as $scope ) {
                $query->{$scope}();
            }
        }

        if ( \Illuminate\Support\Facades\Request::has('in') ) {
            $ins = explode("and", \Illuminate\Support\Facades\Request::get('in'));
            foreach ( $ins as $in ) {
                $x = explode('=', $in);
                $query->whereIn(trim($x[0]), explode(',', trim($x[1])));
            }
        }

        if ( \Illuminate\Support\Facades\Request::has('not_in') ) {
            $ins = explode("and", \Illuminate\Support\Facades\Request::get('not_in'));
            foreach ( $ins as $in ) {
                $x = explode('=', $in);
                $query->whereNotIn(trim($x[0]), explode(',', trim($x[1])));
            }
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    private static function handleAggregation ($query)
    {
        $response['response'] = $query->{\Illuminate\Support\Facades\Request::get('aggregation_operator')}(\Illuminate\Support\Facades\Request::get('aggregation_column'));

        return $response;
    }

    /**
     * @param $sourceType
     * @param $sourceId
     * @return array
     */
    public static function getSourceColumnValue ($sourceType, $sourceId)
    {
        //get the source against which hash is present
        $model = DataModel::where('model_hash', $sourceType)->first();
        //iterate only when the model is present
        if ( $model && $model->display_column ) {
            $className = $model->namespace . '\\' . $model->name;
            $sourceId = $className::find($sourceId);

            //check if record is present or not
            if ( $sourceId )
                $sourceId = $sourceId->{$model->display_column};
        }

        $sourceType = $model ? $model->name : $sourceType;

        //send as a array of both object
        return [$sourceType, $sourceId];
    }
}
