<?php
// /vps/test/vps_form_combo-box-filter_remote-test
class Vps_Form_ComboBoxFilter_RemoteTestController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->setModel(new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'foo'=>2)
            )
        )));

        $foo = new Vps_Form_Field_Select('foo', 'Foo');
        $foo->setFilterField('filter_id');
        $foo->setValues('/vps/test/vps_form_combo-box-filter_remote/json-data')
        ->setAllowBlank(false);

        $this->_form->add(new Vps_Form_Field_ComboBoxFilter('filter', 'Filter'))
            ->setValues(array(
                1 => 'filter1',
                2 => 'filter2',
            ))
            ->setFilteredCombo($foo)
            ;
    }

    protected function _getResourceName()
    {
        return 'vps_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = 1;
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Vps_Form_ComboBoxFilter:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

