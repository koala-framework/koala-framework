<?php
class Kwf_AutoGrid_TestGetExpressionsFilter extends Kwf_Controller_Action_Auto_Filter_Text
{
    protected function _getQueryExpression($q)
    {
        return new Kwf_Model_Select_Expr_Or(
            array(
                new Kwf_Model_Select_Expr_Lower('id', 5),
                new Kwf_Model_Select_Expr_Contains('value', 'a')
            )
        );
    }
}

class Kwf_AutoGrid_TestGetExpressionsController extends Kwf_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = new Kwf_Controller_Action_Auto_FilterCollection();
        $this->_filters->add(new Kwf_AutoGrid_TestGetExpressionsFilter());
    }

}