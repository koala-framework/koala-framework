<?php
class Kwf_AutoGrid_TestWhereEqualsController extends Kwf_AutoGrid_BasicController
{
    protected function _getSelect()
    {
        $expr = new Kwf_Model_Select_Expr_Equal('value', 'Herbert');
        return $this->_model->select()->where($expr);
    }

}