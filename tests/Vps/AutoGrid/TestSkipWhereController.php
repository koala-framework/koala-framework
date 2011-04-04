<?php
class Vps_AutoGrid_TestSkipWhereController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' =>
            array(
                'type'=>'TextField',
                'width'=>80,
                'skipWhere' => true
            )
        );
    }
}