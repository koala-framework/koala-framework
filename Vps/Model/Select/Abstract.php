<?php
abstract class Vps_Model_Select_Abstract
{
    const WHERE_EQUALS       = 'whereEquals';
    const ORDER              = 'order';
    const LIMIT              = 'limit';
    
    protected $_parts = array(); 

    public function __construct(Vps_Model_Abstract $model)
    {
        $this->_model = $model;
    }

    public function whereEquals($field, $value)
    {
        $this->_parts[self::WHERE_EQUALS][$field] = $value;
        return $this;
    }
    
    public function order($field)
    {
        $this->_parts[self::ORDER] = $field;
        return $this;
    }
    
    public function limit($start, $count)
    {
        $this->_parts[self::LIMIT] = array('start' => $start, 'count' => $count);
        return $this;
    }
    
    public function getParts()
    {
        return $this->_parts;
    }
}