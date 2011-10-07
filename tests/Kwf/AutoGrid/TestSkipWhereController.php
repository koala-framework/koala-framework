<?php
class Kwf_AutoGrid_TestSkipWhereController extends Kwf_AutoGrid_BasicController
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