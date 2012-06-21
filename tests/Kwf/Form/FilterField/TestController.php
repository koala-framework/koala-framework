<?php
// /kwf/test/kwf_form_filter-field_test
class Kwf_Form_FilterField_TestController extends Kwf_Controller_Action_Auto_Form
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
        $foo->setValues('/kwf/test/kwf_form_filter-field_remote/json-data');
        $foo->setAllowBlank(false);

        $foo2 = new Kwf_Form_Field_Select('foo2', 'Foo2');
        $foo2->setValues(array(
                1 => 'filter1',
                2 => 'filter2',
        ))
        ->setSave(false)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_FilterField())
            ->setFilterColumn('filter_id')
            ->setFilteredField($foo)
            ->setFilterField($foo2);
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
        $config['assetsType'] = 'Kwf_Form_FilterField:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}
