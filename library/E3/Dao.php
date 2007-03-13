<?php
class E3_Dao
{
    private $_db;
    private $_models;
    
    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function getModel($model)
    {
        if (!isset($this->_models[$model])) {
            $this->_models[$model] = new $model(array('db'=>$this->_db));
        }
        return $this->_models[$model];
    }
    
    public function getDb()
    {
        return $this->_db;
    }
}
