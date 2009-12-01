<?php
class Vps_Model_Select_Expr_Concat extends Vps_Model_Select_Expr_Unary_Abstract
{
    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_STRING;
    }
}