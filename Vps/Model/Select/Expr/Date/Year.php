<?php
class Vps_Model_Select_Expr_Date_Year implements Vps_Model_Select_Expr_Interface
{
    private $_field;

    public function __construct($field)
    {
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Vps_Exception("No Field set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_INTEGER;
    }
}
