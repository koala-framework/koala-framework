<?php
class Vps_AutoGrid_TestSkipWhereController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width'=>80,
            'skipWhere' => true
        );

        parent::_initColumns();
    }
}