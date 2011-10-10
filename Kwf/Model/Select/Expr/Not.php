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

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'expression' => $this->_expression->toArray(),
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls(Kwf_Model_Select_Expr::fromArray($data['expression']));
    }
}