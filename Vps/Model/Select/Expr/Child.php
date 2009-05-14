<?php
class Vps_Model_Select_Expr_Child implements Vps_Model_Select_Expr_Interface
{
    private $_expr;
    private $_child;
    public function __construct($child, Vps_Model_Select_Expr_Interface $expr)
    {
        $this->_child = $child;
        $this->_expr = $expr;
    }

    public function getExpr()
    {
        return $this->_expr;
    }

    public function getChild()
    {
        return $this->_child;
    }

    public function validate()
    {
        $this->_field->validate();
    }

}
