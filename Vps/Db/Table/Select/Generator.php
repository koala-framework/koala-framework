<?php
class Vps_Db_Table_Select_Generator extends Vps_Db_Table_Select
{
    private $_class;
    public function setGeneratorClass($v)
    {
        $this->_class = $v;
        return $this;
    }
    public function getGeneratorClass()
    {
        return $this->_class;
    }
}
