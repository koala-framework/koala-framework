<?php
class Kwf_Model_Select_Expr_Parent_Contains implements Kwf_Model_Select_Expr_Interface
{
    private $_parent;
    private $_select;

    public function __construct($parentRule, Kwf_Model_Select $select=null)
    {
        $this->_parent = $parentRule;
        $this->_select = $select;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function getSelect()
    {
        return $this->_select;
    }

    public function validate()
    {
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }


    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'parent' => $this->_parent,
            'select' => $this->_select ? $this->_select->toArray() : null
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $select = $data['select'] ? Kwf_Model_Select::fromArray($data['select']) : null;
        return new $cls($data['parent'], $select);
    }
}
