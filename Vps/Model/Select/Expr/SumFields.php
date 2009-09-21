<?php
class Vps_Model_Select_Expr_SumFields implements Vps_Model_Select_Expr_Interface
{
    private $_fields;
    public function __construct(array $fields)
    {
        $this->_fields = $fields;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function validate()
    {
        if (count($this->_fields) == 0) {
            throw new Vps_Exception("'".get_class($this)."' has to contain at least one field");
        }
    }
}
