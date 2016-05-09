<?php
class Kwf_Model_Select_Expr_Child_GroupConcat extends Kwf_Model_Select_Expr_Child
{
    public function __construct($child, $field, $separator=',', Kwf_Model_Select $select=null, $orderField=null)
    {
        parent::__construct($child, new Kwf_Model_Select_Expr_GroupConcat($field, $separator, $orderField), $select);
    }
}
