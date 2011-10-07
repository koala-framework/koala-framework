<?php
class Kwf_Model_Select_Expr_Not implements Kwf_Model_Select_Expr_Interface
{
    protected $_expression;
    public function __construct(Kwf_Model_Select_Expr_Interface $expression)
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
            throw new Kwf_Exception("No Expression set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }
}