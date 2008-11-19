<?php
class Vps_Model_Select_Expr_Not implements Vps_Model_Select_Expr_Interface
{
    protected $_expression;
    public function __construct(Vps_Model_Select_Expr_Interface $expression)
    {
        $this->_expression = $expression;
    }
    public function getExpression()
    {
        return $this->_expression;
    }
}