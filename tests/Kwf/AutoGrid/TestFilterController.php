<?php
class Kwf_AutoGrid_TestFilterController extends Kwf_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' => true);
    }
}