<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Subtract extends Kwf_Model_Select_Expr_Unary_Abstract
{
    public $lowerNullAllowed = true;

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_FLOAT;
    }
}