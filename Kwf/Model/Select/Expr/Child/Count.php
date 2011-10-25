<?php
class Kwf_Model_Select_Expr_Child_Count extends Kwf_Model_Select_Expr_Child
{
    public function __construct($child, Kwf_Model_Select $select=null)
    {
        parent::__construct($child, new Kwf_Model_Select_Expr_Count(), $select);
    }
}
