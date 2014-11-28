<?php
// /kwf/test/kwf_form_combo-box-filter_test
class Kwf_Form_ComboBoxFilter_TestController extends Kwf_Controller_Action_Auto_Form
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

//         $this->_form->add(new Kwf_Form_Field_TextField('foo', 'Foo'));

        $foo = new Kwf_Form_Field_Select('foo', 'Foo');
        $foo->setFields(array('id', 'name', 'filter_id'));
        $foo->setFilterField('filter_id');
        $foo->setValues(array(
            array(1, 'test1.1', 1),
            array(2, 'test1.2', 1),
            array(3, 'test1.3', 1),
            array(4, 'test2.4', 2),
            array(5, 'test2.5', 2),
        ))
        ->setAllowBlank(false);
        //$this->_form->add($foo);

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
        $config['assetsPackage'] = new Kwf_Assets_Package_TestPackage('Kwf_Form_ComboBoxFilter');
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

