<?php
class Kwf_Model_Select_Expr_And extends Kwf_Model_Select_Expr_Unary_Abstract
{
    public function toDebug()
    {
        $exprStrings = array();
        foreach ($this->_expressions as $expr) {
            $exprStrings[] = trim(_pArray($expr));
        }
        return '('.implode(') AND (', $exprStrings).')';
    }
}