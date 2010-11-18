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

    public function validate()
    {
        if (!$this->_expression) {
            throw new Vps_Exception("No Expression set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_BOOLEAN;
    }
}