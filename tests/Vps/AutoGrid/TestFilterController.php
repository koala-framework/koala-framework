<?php
class Vps_AutoGrid_TestFilterController extends Vps_AutoGrid_BasicController
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
}