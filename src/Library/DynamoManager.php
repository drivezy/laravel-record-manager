<?php

namespace Drivezy\LaravelRecordManager\Library;

use AWS;
use Drivezy\LaravelUtility\LaravelUtility;

/**
 * Class DynamoManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class DynamoManager
{
    /**
     * DynamoDB client.
     *
     * @var DynamoDbClient
     */
    private static $client = null;

    /**
     * Writes to DynamoDB table.
     *
     * @param string $table the table name
     * @param array $item data to be written
     * @return bool status
     */
    public static function pushToDynamo ($table, $item)
    {
        if ( !LaravelUtility::isInstanceProduction() ) return false;

        self::init();

        self::$client->putItem([
            "TableName"              => $table,
            "Item"                   => $item,
            "ReturnConsumedCapacity" => "NONE",
        ]);

        return true;
    }

    /**
     * Initializes DynamoDb client.
     *
     * This methods should be invoked in every public methods.
     */
    private static function init ()
    {
        if ( self::$client === null ) self::$client = AWS::createClient("DynamoDb");
    }

    /**
     * @param $table
     * @param $items
     * @return bool
     */
    public static function pushMultipleToDynamo ($table, $items)
    {
        if ( !LaravelUtility::isInstanceProduction() ) return false;

        self::init();

        $records = [];
        //preparing items to be pushed out for batch
        foreach ( $items as $item ) {
            array_push($records, [
                'PutRequest' => [
                    'Item' => $item,
                ],
            ]);
        }

        //push the item to the dynamo db
        return self::$client->batchWriteItem([
            'RequestItems' => [
                $table => $records,
            ],
        ]);
    }
}
