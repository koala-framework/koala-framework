<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Boolean implements Kwf_Model_Select_Expr_Interface
{
    protected $_value;

    public function __construct($value)
    {
        $this->_value = (bool)$value;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function validate()
    {
        if (is_null($this->_value)) {
            throw new Kwf_Exception("No Value set for '".get_class($this)."'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'value' => $this->_value,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['value']);
    }
}
