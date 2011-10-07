<?php
class Vps_AutoGrid_TestContainsController extends Vps_AutoGrid_BasicController
{
    protected function _getSelect()
    {
        $expr = new Vps_Model_Select_Expr_Contains('value', 'a');
        return $this->_model->select()->where($expr);
    }

}