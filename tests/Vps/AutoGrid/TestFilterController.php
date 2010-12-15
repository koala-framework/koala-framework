<?php
class Vps_AutoGrid_TestFilterController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' => true);
    }
}