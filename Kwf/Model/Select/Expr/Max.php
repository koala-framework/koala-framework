<?php
class Kwf_Model_Select_Expr_Max implements Kwf_Model_Select_Expr_Interface
{
    private $_field;
    public function __construct($field)
    {
        $this->_field = $field;
    }
    public function getField()
    {
        return $this->_field;
    }

    public function validate()
    {
        $this->_field->validate();
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Vps_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Vps_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Vps_Model_Select_Expr::fromArray($field);
        }
        return new $cls($field);
    }
}
