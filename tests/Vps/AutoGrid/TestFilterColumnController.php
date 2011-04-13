<?php
class Vps_AutoGrid_TestFilterColumnController extends Vps_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = new Vps_Controller_Action_Auto_FilterCollection();

        $this->_filters->add(new Vps_Controller_Action_Auto_Filter_TextColumn())
            ->setFilterFields(array(
                'value' => 'Wert',
                'testtime' => 'Testzeit',
                'type' => 'Typ'
            ));
    }
}