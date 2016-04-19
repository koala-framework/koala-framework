<?php
class Kwf_Model_Select_Expr_GroupConcat implements Kwf_Model_Select_Expr_Interface
{
    private $_field;
    private $_separator;
    private $_orderField;
    public function __construct($field, $separator=',', $orderField=null)
    {
        $this->_field = $field;
        $this->_separator = $separator;
        $this->_orderField = $orderField;
    }
    public function getField()
    {
        return $this->_field;
    }

    public function getSeparator()
    {
        return $this->_separator;
    }

    public function getOrderField()
    {
        return $this->_orderField;
    }

    public function validate()
    {
        $this->_field->validate();
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_STRING;
    }

    public function toArray()
    {
        $field = $this->_field;
        $orderField = $this->_orderField;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        if ($orderField instanceof Kwf_Model_Select_Expr_Interface) $orderField = $orderField->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'orderField' => $orderField,
            'separator' => $this->_separator,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field, $data['separator']);
        }
        return new $cls($field);
    }
}
