<?php

namespace Drivezy\LaravelRecordManager\Library;

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class AuditManager
 * @package Drivezy\LaravelRecordManager\Library
 */
class AuditManager {
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
    private $restrictedFields = [];
    /**
     * @var array|mixed
     */
    private $auditDisabled = [];

    /**
     * @var array
     */
    private $records = [];

    /**
     * AuditManager constructor.
     * @param Eloquent $model
     */
    public function __construct (Eloquent $model) {
        $this->model = $model;

        $this->hash = md5($model->getActualClassNameForMorph($model->getMorphClass()));
        $this->restrictedFields = ['updated_at', 'updated_by', 'deleted_at'];
        $this->auditFields = $model->auditEnabled;
        $this->auditDisabled = $model->auditDisabled;
    }

    /**
     * @return bool
     */
    public function setAuditRecord () {
        if ( !$this->isAuditable() ) return false;

        if ( $this->isInsertOperation() ) return false;

        foreach ( $this->model->getDirty() as $attribute => $value ) {
            if ( !$this->checkAuditConditions($attribute) ) continue;

            if ( !$this->checkNumberField($attribute) ) continue;

            $this->largeAuditLog($attribute);
        }
    }

    /**
     * @return bool
     */
    private function isInsertOperation () {
        if ( $this->model->getOriginal('id') != $this->model->getAttribute('id') ) return true;

        return false;
    }

    /**
     * @return bool
     */
    private function isAuditable () {
        if ( $this->model->auditable ) return true;

        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    private function checkAuditConditions ($attribute) {
        if ( in_array($attribute, $this->restrictedFields) )
            return false;

        if ( sizeof($this->auditFields) ) {
            if ( !in_array($attribute, $this->auditFields) )
                return false;
        }

        if ( sizeof($this->auditDisabled) ) {
            if ( in_array($attribute, $this->auditDisabled) )
                return false;
        }

        return true;
    }

    /**
     * @param $attribute
     * @return bool
     */
    private function checkNumberField ($attribute) {
        $currentValue = $this->model->getAttribute($attribute);
        $originalValue = $this->model->getOriginal($attribute);

        if ( !( is_numeric($currentValue) || is_numeric($originalValue) ) ) return true;

        if ( round($currentValue, 2) == round($originalValue, 2) ) return false;

        return true;
    }

    /**
     * @param $attribute
     * @return bool
     */
    private function largeAuditLog ($attribute) {
        $currentValue = $this->convertObjectToString($this->model->getAttribute($attribute)) ? : 'null';
        $originalValue = $this->convertObjectToString($this->model->getOriginal($attribute)) ? : 'null';

        $updatedBy = $this->model->updated_by ? : 'null';

        $item = [
            'model_hash' => ['S' => '' . $this->hash . '-' . $this->model->id . ''],
            'parameter'  => ['S' => '' . $attribute . ''],
            'old_value'  => ['S' => '' . $originalValue . ''],
            'new_value'  => ['S' => '' . $currentValue . ''],
            'created_at' => ['N' => '' . strtotime($this->model->updated_at) . ''],
            'created_by' => ['S' => '' . $updatedBy . ''],
        ];

        array_push($this->records, $item);

        return false;
    }

    /**
     * @param $str
     * @return string
     */
    private function convertObjectToString ($str) {
        if ( is_array($str) || is_object($str) )
            return serialize($str);

        return $str;
    }

    /**
     * record the items on dynamo db
     */
    public function __destruct () {
        $table = LaravelUtility::getProperty('dynamo.audit.table', 'dz_audit_logs');
        DynamoManager::pushToDynamo($table, $this->records);
    }
}
