<?php

namespace Drivezy\LaravelRecordManager\Models;

use AWS;
use Drivezy\LaravelRecordManager\Library\ModelManager;
use Drivezy\LaravelUtility\Library\DateUtil;

/**
 * Class DynamoEloquentTrait
 * @package Drivezy\LaravelRecordManager\Library
 */
trait DynamoEloquentTrait
{
    /**
     * @var array
     */
    public $dynamo_columns = [];

    /**
     * @var array
     */
    public $prefetch_dynamo_columns = false;

    /**
     * @var string
     */
    private $dynamo_table = 'dz_model_data';

    /**
     * Find the related model column related data from the dynamo db
     * @param array $attributes
     * @return array
     */
    public function getDynamoAttributes ($attributes = [])
    {
        //see the attributes are being sent or not
        //if nothing is pushed, then find all attributes which are defined as dynamo column
        $attributes = sizeof($attributes) ? $attributes : ModelManager::getDynamoColumns($this->class_hash);

        $record = $this->getModelRecord();
        $response = [];

        //iterate through all columns and get the related data from the dynamo object
        foreach ( $attributes as $attribute ) {
            //DANGER!!! if making changes here, make changes in setDynamoAttributes method also else conversion will fail
//            $response[ $attribute ] = isset($record[ $attribute ]['S']) ? unserialize($record[ $attribute ]['S']) : null;
            $response[ $attribute ] = isset($record[ $attribute ]['S']) ? json_decode($record[ $attribute ]['S']) : null;
        }
        $response['last_updated_at'] = $record['last_updated_at']['S'];

        return $response;
    }

    /**
     * get the dynamo column data from the dynamo db against the
     * given model and then push first element as dynamo object
     * @return bool
     */
    private function getModelRecord ()
    {
        //create dynamo client
        $client = AWS::createClient("DynamoDb");

        //query the db against the given elements
        $iterator = $client->getIterator('Query',
            array(
                'TableName'     => $this->dynamo_table,
                'KeyConditions' => array(
                    'model_hash' => array(
                        'AttributeValueList' => array(
                            array('S' => '' . $this->class_hash . ''),
                        ),
                        'ComparisonOperator' => 'EQ',
                    ),
                    'model_id'   => array(
                        'AttributeValueList' => array(
                            array('N' => '' . $this->id . ''),
                        ),
                        'ComparisonOperator' => 'EQ',
                    ),
                ),
            ),
            array(
                'limit' => 1,
            ));

        //return first element which every is returned from the request
        foreach ( $iterator as $item ) {
            return $item;
        }

        return [];
    }

    /**
     * push the columns that are defined as dynamo column to the dynamodb
     * @param $attributes
     */
    public function setDynamoAttributes ($attributes = [])
    {
        //default to system defined dynamo columns if nothing is being sent
        $attributes = sizeof($attributes) ? $attributes : $this->dynamo_columns;

        //if no new attributes are found return
        if ( !count($attributes) )
            return;

        //get current data from dynamo db
        $currentData = $this->getModelRecord();

        //merge old data with new data and override matching keys with new data
        $currentData = array_merge($currentData, $attributes);
        if ( !count($currentData) ) return;

        //preparing data as per the dynamo schema
        foreach ( $attributes as $key => $value ) {
            //DANGER!!! if making changes here, make changes in getDynamoAttributes method also else conversion will fail
//            $currentData[ $key ] = ['S' => '' . serialize($value) . ''];
            $currentData[ $key ] = ['S' => '' . json_encode($value) . ''];
        }

        //touching updated_at column when new data is being pushed
        $currentData['last_updated_at'] = ['S' => '' . DateUtil::getDateTime() . ''];

        $this->setModelRecord($currentData);

    }

    /**
     * save the items to the dynamo db
     * @param $item
     */
    private function setModelRecord ($item)
    {
        $client = AWS::createClient("DynamoDb");
        $client->putItem([
            "TableName"              => $this->dynamo_table,
            "Item"                   => $item,
            "ReturnConsumedCapacity" => "NONE",
        ]);
    }

    /**
     * remove the dynamo columns from the regular model object
     * and then set it aside in the dynamo element which can then
     * be pushed to the db post final saving
     */
    public function setDynamoColumns ()
    {
        //get columns defined as dynamo column from the db
        $columns = ModelManager::getDynamoColumns($this->class_hash);

        foreach ( $columns as $column ) {
            //check if the given column is present in the given schema
            if ( !isset($this->{$column}) ) continue;

            //set the dynamo column property
            $this->dynamo_columns[ $column ] = $this->{$column};

            //remove those columns from the transaction db schema
            unset($this->{$column});
        }
    }
}
