<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use AWS;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class AuditLogController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class AuditLogController extends Controller
{

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getAuditLog ($id, Request $request)
    {
        $model = DataModel::find($id);
        if ( !$model ) return invalid_operation();

        $table = LaravelUtility::getProperty('dynamo.audit.table', 'dz_audit_logs');

        $client = AWS::createClient('DynamoDb');
        $iterator = $client->getIterator('Query',
            array(
                'TableName'     => $table,
                'KeyConditions' => array(
                    'model_hash' => array(
                        'AttributeValueList' => array(
                            array('S' => '' . $model->model_hash . '-' . $request->record_id . ''),
                        ),
                        'ComparisonOperator' => 'EQ',
                    ),
                    'created_at' => array(
                        'AttributeValueList' => array(
                            array('N' => '' . 0 . ''),
                        ),
                        'ComparisonOperator' => 'GT',
                    ),
                ),
            ),
            array(
                'limit' => 200,
            ));

        $logs = [];
        $userClass = LaravelUtility::getUserModelFullQualifiedName();
        foreach ( $iterator as $item ) {

            $log = [
                'created_at'   => DateUtil::getDateTime($item['created_at']['N'] / 1000),
                'parameter'    => $item['parameter']['S'],
                'old_value'    => $item['old_value']['S'],
                'new_value'    => $item['new_value']['S'],
                'created_by'   => $item['created_by']['S'],
                'created_user' => $userClass::find($item['created_by']['S']),
            ];
            array_push($logs, $log);
        }

        return success_response($logs);
    }
}
