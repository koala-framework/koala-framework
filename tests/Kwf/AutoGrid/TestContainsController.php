<?php
class Kwf_AutoGrid_TestContainsController extends Kwf_AutoGrid_BasicController
{
    protected function _getSelect()
    {
        $expr = new Kwf_Model_Select_Expr_Contains('value', 'a');
        return $this->_model->select()->where($expr);
    }

}