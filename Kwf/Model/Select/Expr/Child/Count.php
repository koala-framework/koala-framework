<?php
class Vps_Model_Select_Expr_Child_Count extends Vps_Model_Select_Expr_Child
{
    public function __construct($child, Vps_Model_Select $select=null)
    {
        parent::__construct($child, new Vps_Model_Select_Expr_Count(), $select);
    }
}
