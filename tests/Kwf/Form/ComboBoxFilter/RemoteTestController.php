<?php
// /kwf/test/kwf_form_combo-box-filter_remote-test
class Kwf_Form_ComboBoxFilter_RemoteTestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->setModel(new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'foo'=>2)
            )
        )));

        $foo = new Kwf_Form_Field_Select('foo', 'Foo');
        $foo->setFilterField('filter_id');
        $foo->setValues('/kwf/test/kwf_form_combo-box-filter_remote/json-data')
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_ComboBoxFilter('filter', 'Filter'))
            ->setValues(array(
                1 => 'filter1',
                2 => 'filter2',
            ))
            ->setFilteredCombo($foo)
            ;
    }

    protected function _getResourceName()
    {
        return 'kwf_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = 1;
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Kwf_Form_ComboBoxFilter:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

