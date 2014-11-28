<?php
class Kwf_Model_Select_Expr_GroupConcat implements Kwf_Model_Select_Expr_Interface
{
    private $_field;
    private $_separator;
    public function __construct($field, $separator=',')
    {
        $this->_field = $field;
        $this->_separator = $separator;
    }
    public function getField()
    {
        return $this->_field;
    }

    public function getSeparator()
    {
        return $this->_separator;
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
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
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
