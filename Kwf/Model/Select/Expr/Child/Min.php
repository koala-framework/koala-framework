<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Child_Min extends Kwf_Model_Select_Expr_Child
{
    public function __construct($child, $field)
    {
        parent::__construct($child, new Kwf_Model_Select_Expr_Min($field));
    }
}
