<?php
class Vps_Model_Select_Expr_Concat extends Vps_Model_Select_Expr_Unary_Abstract
{
    public function getResultType()
    {
        return sVps_Model_Interfaceelf::TYPE_STRING;
    }
}