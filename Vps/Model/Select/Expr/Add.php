<?php
class Vps_Model_Select_Expr_Add extends Vps_Model_Select_Expr_Unary_Abstract
{
    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_INTEGER;
    }
}