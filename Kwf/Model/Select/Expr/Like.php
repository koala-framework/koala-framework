<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Like extends Kwf_Model_Select_Expr_CompareField_Abstract
{
    public function toDebug()
    {
        return trim(_pArray($this->_field)).' LIKE \''.trim(_pArray($this->_value)).'\'';
    }
}