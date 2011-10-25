<?php
class Kwf_Model_Select_Expr_Field implements Kwf_Model_Select_Expr_Interface
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
        return null;
    }

    public function toArray()
    {
        $field = $this->_field;
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['field']);
    }
}
