<?php
class Vps_AutoGrid_TestGetExpressionsFilter extends Vps_Controller_Action_Auto_Filter_Text
{
    protected function _getQueryExpression($q)
    {
        return new Vps_Model_Select_Expr_Or(
            array(
                new Vps_Model_Select_Expr_Lower('id', 5),
                new Vps_Model_Select_Expr_Contains('value', 'a')
            )
        );
    }
}

class Vps_AutoGrid_TestGetExpressionsController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = new Vps_Controller_Action_Auto_FilterCollection();
        $this->_filters->add(new Vps_AutoGrid_TestGetExpressionsFilter());
    }

}