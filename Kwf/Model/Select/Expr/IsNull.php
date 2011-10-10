<?php
class Kwf_Model_Select_Expr_IsNull implements Kwf_Model_Select_Expr_Interface
{
    protected $_field;

    public function __construct($field) {
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Kwf_Exception("No Field-Value set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
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
