<?php
class Vps_Model_Select_Expr_Child_Contains implements Vps_Model_Select_Expr_Interface
{
    private $_child;
    private $_select;

    public function __construct($child, Vps_Model_Select $select=null)
    {
        $this->_child = $child;
        $this->_select = $select;
    }

    public function getChild()
    {
        return $this->_child;
    }

    public function getSelect()
    {
        return $this->_select;
    }

    public function validate()
    {
    }

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_BOOLEAN;
    }
}
