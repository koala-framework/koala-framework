<?php
class Vps_Model_Db_Select implements Vps_Model_Select_Interface
{
    protected $_select;
    protected $_tableName;
    protected $_model;
    public function __construct(Vps_Model_Db $model)
    {
        $this->_model = $model;
        $this->_select = $model->getTable()->select();
        $this->_tableName = $model->getTable()->info('name');
    }
    public function whereEquals($field, $value)
    {
        if (is_array($value)) {
            $quotedValues = array();
            foreach ($value as $v) {
                $quotedValues[] = $this->_model->getTable()->getAdapter()->quote($v);
            }
            $quotedValues = implode(', ', $quotedValues);
            $this->_select->where("{$this->_tableName}.$field IN ($quotedValues)");
        } else {
            $this->_select->where("{$this->_tableName}.$field = ?", $value);
        }
        return $this;
    }
    
    public function order($field)
    {
        $this->_select->order($field);
        return $this;
    }
    
    public function limit($start, $count)
    {
        $this->_select->limit($start, $count);
        return $this;
    }
    
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->_select, $method), $arguments);
    }
    
    public function getDbSelect()
    {
        return $this->_select;
    }
}
