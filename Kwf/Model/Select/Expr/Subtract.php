<?php
class Vps_Model_Select_Expr_Subtract extends Vps_Model_Select_Expr_Unary_Abstract
{
    public $lowerNullAllowed = true;

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_INTEGER;
    }
}