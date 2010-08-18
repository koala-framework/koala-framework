<?php
class Vps_AutoGrid_TestWhereEqualsController extends Vps_AutoGrid_BasicController
{
    protected function _getSelect()
    {
        $expr = new Vps_Model_Select_Expr_Equal('value', 'Herbert');
        return $this->_model->select()->where($expr);
    }

}