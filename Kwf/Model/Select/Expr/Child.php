<?php
class Kwf_Model_Select_Expr_Child implements Kwf_Model_Select_Expr_Interface
{
    private $_child;
    private $_expr;
    private $_select;

    public function __construct($child, Kwf_Model_Select_Expr_Interface $expr, Kwf_Model_Select $select=null)
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

    public function getResultType()
    {
        return $this->_expr->getResultType();
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'child' => $this->_child,
            'expr' => $this->_expr->toArray(),
            'select' => $this->_select ? $this->_select->toArray() : null
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $select = $data['select'] ? Kwf_Model_Select::fromArray($data['select']) : null;
        return new $cls($data['child'], Kwf_Model_Select_Expr::fromArray($data['expr']), $select);
    }
}
