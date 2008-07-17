<?php
class Vps_Db_Table_Select_Generator extends Vps_Db_Table_Select
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
}
