<?php
abstract class Kwf_Model_Select_Expr_Unary_Abstract
    implements Kwf_Model_Select_Expr_Interface, ArrayAccess
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
    public function addExpression($x)
    {
        $this->_expressions[] = $x;
        return $this;
    }

    public function offsetExists($offset)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }
    public function offsetGet($offset)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }
    public function offsetSet($offset, $value)
    {
        if (!$offset) {
            $this->_expressions[] = $value;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }
    public function offsetUnset($offset)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function validate ()
    {
        if (count($this->_expressions) == 0) {
            throw new Kwf_Exception("'".get_class($this)."' has to contain at least one Expression");
        }
        foreach ($this->_expressions as $expression) {
            $expression->validate();
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }
}