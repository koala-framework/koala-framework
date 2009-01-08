<?php
abstract class Vps_Model_Select_Expr_Unary_Abstract implements Vps_Model_Select_Expr_Interface
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

    public function validate ()
    {
        if (count($this->_expressions) == 0) {
            throw new Vps_Exception("'".get_class($this)."' hast to contain at least one Expression");
        }
        foreach ($this->_expressions as $expression) {
            $expression->validate();
        }
    }
}