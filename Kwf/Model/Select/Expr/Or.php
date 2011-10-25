<?php
class Kwf_Model_Select_Expr_Or extends Kwf_Model_Select_Expr_Unary_Abstract
{
    public function toDebug()
    {
        $exprStrings = array();
        foreach ($this->_expressions as $expr) {
            $exprStrings[] = trim(_pArray($expr, '    '));
        }
        return '('.implode(")\n    OR (", $exprStrings).')';
    }
}
