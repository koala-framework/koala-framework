<?php
class Vps_Model_Db_TestAdapter extends Zend_Db_Adapter_Abstract
{
    public function __construct() {}
    public function listTables() {}
    public function describeTable($tableName, $schemaName = null) {}
    protected function _connect() {}
    public function closeConnection() {}
    public function prepare($sql) {}
    public function lastInsertId($tableName = null, $primaryKey = null) {}
    protected function _beginTransaction() {}
    protected function _commit() {}
    protected function _rollBack() {}
    public function setFetchMode($mode) {}
    public function limit($sql, $count, $offset = 0) {}
    public function supportsParameters($type) {}
    public function getServerVersion() {}
    public function isConnected() { return true; }
}
