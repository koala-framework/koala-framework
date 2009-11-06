<?php
class Vps_Model_Select_Expr_Field implements Vps_Model_Select_Expr_Interface
{
    protected $_field;

    public function __construct($field) {
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Vps_Exception("No Field-Value set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return null;
    }
}
