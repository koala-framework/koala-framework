<?php
class Vps_Model_Select_Expr_Child implements Vps_Model_Select_Expr_Interface
{
    private $_child;
    private $_expr;
    private $_select;

    public function __construct($child, Vps_Model_Select_Expr_Interface $expr, Vps_Model_Select $select=null)
    {
        $this->_child = $child;
        $this->_expr = $expr;
        $this->_select = $select;
    }

    public function getExpr()
    {
        return $this->_expr;
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
        $this->_expr->validate();
    }

}
