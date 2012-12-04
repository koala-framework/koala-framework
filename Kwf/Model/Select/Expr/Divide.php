<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Divide extends Kwf_Model_Select_Expr_Unary_Abstract
{
    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }
}