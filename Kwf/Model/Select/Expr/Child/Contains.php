<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Child_Contains implements Kwf_Model_Select_Expr_Interface
{
    private $_child;
    private $_select;

    public function __construct($child, Kwf_Model_Select $select=null)
    {
        $this->_child = $child;
        $this->_select = $select;
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
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }


    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'child' => $this->_child,
            'select' => $this->_select ? $this->_select->toArray() : null
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $select = $data['select'] ? Kwf_Model_Select::fromArray($data['select']) : null;
        return new $cls($data['child'], $select);
    }
}
