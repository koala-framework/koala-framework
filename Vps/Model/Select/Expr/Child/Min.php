<?php
class Vps_Model_Select_Expr_Child_Min extends Vps_Model_Select_Expr_Child
{
    public function __construct($child, $field)
    {
        parent::__construct($child, new Vps_Model_Select_Expr_Min($field));
    }
}
