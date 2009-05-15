<?php
class Vps_Model_Select_Expr_Count implements Vps_Model_Select_Expr_Interface
{
    private $_field;
    private $_distinct;

    public function __construct($field = '*', $distinct = false)
    {
        $this->_field = $field;
        $this->_distinct = (bool)$distinct;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getDistinct()
    {
        return $this->_distinct;
    }

    public function validate()
    {
    }
}
