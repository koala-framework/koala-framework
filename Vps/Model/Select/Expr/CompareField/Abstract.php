<?php
abstract class Vps_Model_Select_Expr_CompareField_Abstract implements Vps_Model_Select_Expr_Interface
{
    protected $_field;
    protected $_value;
    public function __construct($field, $value) {
        $this->_field = $field;
        $this->_value = $value;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getValue()
    {
        return $this->_value;
    }
}