<?php
/**
 * @package Validate
 */
abstract class Kwf_Validate_Row_Abstract extends Zend_Validate_Abstract
{
    public function setField($field)
    {
        $this->_field = $field;
    }
    abstract public function isValidRow($value, $row);
    public function isValid($value)
    {
        return $this->isValidRow($value, null);
    }
}
