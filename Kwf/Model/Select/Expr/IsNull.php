<?php
class Kwf_Model_Select_Expr_IsNull implements Kwf_Model_Select_Expr_Interface
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
            throw new Kwf_Exception("No Field-Value set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }
}
