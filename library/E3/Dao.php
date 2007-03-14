<?php
class E3_Dao
{
    private $_db;
    private $_tables;
    
    public function __construct(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
    }

    public function getTable($tablename)
    {
        if (!isset($this->_tables[$tablename])) {
            Zend::loadClass($tablename);
            $this->_tables[$tablename] = new $tablename(array('db'=>$this->_db));
        }
        return $this->_tables[$tablename];
    }
    
    public function getDb()
    {
        return $this->_db;
    }
}
