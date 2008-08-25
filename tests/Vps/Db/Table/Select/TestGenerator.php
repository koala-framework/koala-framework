<?php
class Vps_Db_Table_Select_TestGenerator
{
    private $_generator;
    
    public function setGenerator($v)
    {
        $this->_generator = $v;
        return $this;
    }
    
    public function getGenerator()
    {
        return $this->_generator;
    }
    
    public function __call($a, $b)
    {
       return $this;
    }
}
