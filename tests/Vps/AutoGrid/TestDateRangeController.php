<?php
class Vps_AutoGrid_TestDateRangeController extends Vps_AutoGrid_BasicController
{
    protected function _initColumns()
    {
        $this->_filters['testtime'] = array(
            'type'=>'DateRange',
            'width'=>80
        );

        parent::_initColumns();
    }

}