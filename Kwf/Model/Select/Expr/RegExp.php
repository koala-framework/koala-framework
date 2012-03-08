<?php
class Kwf_Model_Select_Expr_RegExp extends Kwf_Model_Select_Expr_CompareField_Abstract
{
    public function toDebug()
    {
        return trim(_pArray($this->_field)).' REGEXP \''.trim(_pArray($this->_value)).'\'';
    }
}
