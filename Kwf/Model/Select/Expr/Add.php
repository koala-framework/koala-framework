<?php
class Kwf_Model_Select_Expr_Add extends Kwf_Model_Select_Expr_Unary_Abstract
{
    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }
}