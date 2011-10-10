<?php
class Kwf_Model_Select_Expr_Count implements Kwf_Model_Select_Expr_Interface
{
    private $_field;
    private $_distinct;

    public function __construct($field = '*', $distinct = false)
    {
        $this->_field = $field;
        $this->_distinct = (bool)$distinct;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getDistinct()
    {
        return $this->_distinct;
    }

    public function validate()
    {
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'distinct' => $this->_disting,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field);
        }
        return new $cls($field, $data['distinct']);
    }
}
