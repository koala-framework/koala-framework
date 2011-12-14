<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Child_Sum extends Kwf_Model_Select_Expr_Child
{
    public function __construct($child, $field, Kwf_Model_Select $select=null)
    {
        parent::__construct($child, new Kwf_Model_Select_Expr_Sum($field), $select);
    }
}
