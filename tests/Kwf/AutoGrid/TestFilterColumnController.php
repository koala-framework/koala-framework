<?php
class Kwf_AutoGrid_TestFilterColumnController extends Kwf_AutoGrid_BasicController
{
    protected $_querySeparator = ',';
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = new Kwf_Controller_Action_Auto_FilterCollection();

        $this->_filters->add(new Kwf_Controller_Action_Auto_Filter_TextColumn())
            ->setFilterFields(array(
                'value' => 'Wert',
                'testtime' => 'Testzeit',
                'type' => 'Typ'
            ));
    }
}