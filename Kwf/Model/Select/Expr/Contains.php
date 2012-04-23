<?php
class Kwf_Model_Select_Expr_Contains extends Kwf_Model_Select_Expr_Like
{
    public function toDebug()
    {
        return trim(_pArray($this->_field)).' CONTAINS \''.trim(_pArray($this->_value)).'\'';
    }
}
