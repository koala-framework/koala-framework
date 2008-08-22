<?php
class Vps_Test_Db_Table_Select_Generator
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
    
    public function __call()
    {
       return $this;
    }
}
