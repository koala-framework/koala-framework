<?php
class Vps_AutoGrid_TestGetExpressionsController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width'=>80
        );
        parent::_initColumns();
    }

    protected function _getQueryExpression($q)
    {
        return new Vps_Model_Select_Expr_Or(
            array(
                new Vps_Model_Select_Expr_Smaller('id', 5),
                new Vps_Model_Select_Expr_Contains('value', 'a')
            )
        );
    }
}