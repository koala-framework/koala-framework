<?php
class Vps_Model_Select_Expr_Or implements Vps_Model_Select_Expr_Interface
{
    protected $_expressions;
    public function __construct(array $expressions)
    {
        $this->_expressions = $expressions;
    }
    public function getExpressions()
    {
        return $this->_expressions;
    }
}