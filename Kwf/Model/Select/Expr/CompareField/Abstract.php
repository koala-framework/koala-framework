<?php
/**
 * @package Model
 * @subpackage Expr
 */
abstract class Kwf_Model_Select_Expr_CompareField_Abstract implements Kwf_Model_Select_Expr_Interface
{
    protected $_field;
    protected $_value;
    public function __construct($field, $value) {
        $this->_field = $field;
        $this->_value = $value;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getFormattedValue()
    {
        $ret = $this->getValue();
        if ($ret instanceof Kwf_DateTime) {
            $ret = $ret->format('Y-m-d H:i:s');
        } else if ($ret instanceof Kwf_Date) {
            $ret = $ret->format('Y-m-d');
        }
        return $ret;
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
        $value = $this->_value;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        if ($value instanceof Kwf_Model_Select_Expr_Interface) $value = $value->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'value' => $value,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        $value = $data['value'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field);
        }
        if (is_array($value)) {
            $value = Kwf_Model_Select_Expr::fromArray($value);
        }
        return new $cls($field, $value);
    }
}