<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelRecordManager\Models\AuditLog;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class AuditManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class AuditManager
{
    /**
     * @var Eloquent|null
     */
    private $model = null;
    /**
     * @var null|string
     */
    private $hash = null;

    /**
     * @var array|mixed
     */
    private $auditFields = [];
    /**
     * @var array
     */
    private $restrictedFields = ['updated_at', 'updated_by', 'deleted_at'];
    /**
     * @var array|mixed
     */
    private $auditDisabled = [];

    /**
     * @var array
     */
    private $records = [];

    /**
     * @var array
     */
    private $keys = [];

    private $doubleAuditEnabled = [];

    /**
     * AuditManager constructor.
     * @param Eloquent $model
     */
    public function __construct (Eloquent $model)
    {
        $this->model = $model;
        $this->doubleAuditEnabled = ModelManager::getDoubleAuditColumns($model->class_hash);

    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process ()
    {
        //check if auditing is enabled at the global level
        if ( !$this->isAuditable() ) return false;

        //dont enable logging for the insert operation
        if ( $this->isInsertOperation() ) return false;

        //iterate through each entity and find the columns that were updated
        foreach ( $this->model->getDirty() as $attribute => $value ) {
            //check if we have disabled audit log for the given column
            if ( !$this->checkAuditConditions($attribute) ) continue;

            //number fields are usually passed on as string and needs to be checked
            //that they are actually the same number or else at times
            //we create audit record for same data like 2 and 2.0
            if ( !$this->checkNumberField($attribute) ) continue;


            //create audit log record against the item
            $this->recordAuditLog($attribute);
        }
    }

    /**
     * if id is present on the original record then its not an insert operation
     * @return bool
     */
    private function isInsertOperation ()
    {
        if ( $this->model->getOriginal('id') != $this->model->getAttribute('id') ) return true;

        return false;
    }

    /**
     * Check at high level if the model in itself is auditable or not
     * @return bool
     */
    private function isAuditable ()
    {
        //create audit record only on production instance
        if ( !LaravelUtility::isInstanceProduction() ) return false;

        //check if the model is auditable or not
        if ( $this->model->auditable ) return true;

        return false;
    }

    /**
     * Check individually if the given column is to be audited or not
     * @param $attribute
     * @return bool
     */
    private function checkAuditConditions ($attribute)
    {
        //check if the columns fall under the restricted one
        if ( in_array($attribute, $this->restrictedFields) )
            return false;

        //see if the column is defined only for audit
        if ( sizeof($this->model->auditEnabled) ) {
            if ( !in_array($attribute, $this->model->auditEnabled) )
                return false;
        }

        //check if the column is disabled for auditing
        if ( sizeof($this->model->auditDisabled) ) {
            if ( in_array($attribute, $this->model->auditDisabled) )
                return false;
        }

        return true;
    }

    /**
     * Check if the given string is a number field and the number field is same
     * to differentiate between 2.0 and 2 so that both the records are same
     * @param $attribute
     * @return bool
     */
    private function checkNumberField ($attribute)
    {
        $currentValue = $this->model->getAttribute($attribute);
        $originalValue = $this->model->getOriginal($attribute);

        if ( !( is_numeric($currentValue) || is_numeric($originalValue) ) ) return true;

        if ( round($currentValue, 2) == round($originalValue, 2) ) return false;

        return true;
    }

    /**
     * create record entity for the audit log logging against the user.
     * data is in particular for the dynamo-db standard
     * @param $attribute
     * @throws \Exception
     */
    private function recordAuditLog ($attribute)
    {
        $currentValue = $this->convertObjectToString($this->model->getAttribute($attribute)) ? : 'null';
        $originalValue = $this->convertObjectToString($this->model->getOriginal($attribute)) ? : 'null';

        $updatedBy = $this->model->updated_by ? : 'null';

        $time = $this->getTime();

        //create record for the dynamodb record
        $item = [
            'model_hash' => ['S' => '' . $this->model->class_hash . '-' . $this->model->id . ''],
            'parameter'  => ['S' => '' . $attribute . ''],
            'old_value'  => ['S' => '' . $originalValue . ''],
            'new_value'  => ['S' => '' . $currentValue . ''],
            'created_at' => ['N' => '' . $time . ''],
            'created_by' => ['S' => '' . $updatedBy . ''],
        ];
        array_push($this->records, $item);

        //see if audit for transactional db is enabled or not
        if ( !in_array($attribute, $this->doubleAuditEnabled) ) return;

        //create a db record in transactional db
        AuditLog::create([
            'model_hash' => $this->model->class_hash,
            'model_id'   => $this->model->id,
            'parameter'  => $attribute,
            'old_value'  => $originalValue,
            'new_value'  => $currentValue,
            'created_by' => $updatedBy,
        ]);
    }


    /**
     * if any given element is object, then convert it into string
     * @param $str
     * @return string
     */
    private function convertObjectToString ($str)
    {
        if ( is_array($str) || is_object($str) )
            return serialize($str);

        return $str;
    }

    /**
     * @return bool|float|int
     * @throws \Exception
     */
    private function getTime ()
    {
        $masterKey = $this->model->class_hash . '-' . $this->model->id;
        while ( true ) {
            $time = strtotime($this->model->updated_at) * 1000 + random_int(0, 999);
            if ( in_array($masterKey . '-' . $time, $this->keys) ) continue;

            array_push($this->keys, $masterKey . '-' . $time);

            return $time;
        }

        return false;
    }

    /**
     * once the class makes its fall, insert all pending record into dynamo-db
     */
    public function __destruct ()
    {
        //if no records to push to audit, then leave dont call the dynamo push job
        if ( !sizeof($this->records) ) return;

        //get the table onto which audit logs are to be pushed to
        $table = LaravelUtility::getProperty('dynamo.audit.table', 'dz_audit_logs');
        DynamoManager::pushMultipleToDynamo($table, $this->records);
    }
}
