<?php
// /kwf/test/kwf_form_field-set_strage-blue-line_test
class Kwf_Form_FieldSet_StrageBlueLine_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $m = new Kwf_Model_FnF();
        $row = $m->createRow();
        $row->id = 1;
        $row->fs = 1;
        $row->asdf = 'Test';
        $row->save();
        $this->_form->setModel($m);
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet('Foobar'))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs');
            $fs->add(new Kwf_Form_Field_TextField('asdf', 'Asdf'));
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
        $config['assetsType'] = 'Kwf_Form_FieldSet_StrageBlueLine:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

