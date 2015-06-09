<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Sum implements Kwf_Model_Select_Expr_Interface
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
        return Kwf_Model_Interface::TYPE_FLOAT;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field);
        }
        return new $cls($field);
    }
}
