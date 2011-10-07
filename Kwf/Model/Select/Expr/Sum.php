<?php
class Kwf_Model_Select_Expr_Sum implements Kwf_Model_Select_Expr_Interface
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
        $this->_field->validate();
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }
}
