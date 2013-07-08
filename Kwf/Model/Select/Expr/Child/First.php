<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Child_First implements Kwf_Model_Select_Expr_Interface
{
    private $_child;
    private $_field;
    private $_select;

    public function __construct($child, $field, Kwf_Model_Select $select=null)
    {
        $this->_child = $child;
        $this->_field = $field;
        $this->_select = $select;
    }

    public function getChild()
    {
        return $this->_child;
    }

    public function getField()
    {
        return $this->_field;
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
        return null;
    }


    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'child' => $this->_child,
            'field' => $this->_field,
            'select' => $this->_select ? $this->_select->toArray() : null
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $select = $data['select'] ? Kwf_Model_Select::fromArray($data['select']) : null;
        return new $cls($data['child'], $data['field'], $select);
    }
}
