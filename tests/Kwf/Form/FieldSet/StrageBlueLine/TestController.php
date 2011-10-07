<?php
// /vps/test/vps_form_field-set_strage-blue-line_test
class Vps_Form_FieldSet_StrageBlueLine_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $m = new Vps_Model_FnF();
        $row = $m->createRow();
        $row->id = 1;
        $row->fs = 1;
        $row->asdf = 'Test';
        $row->save();
        $this->_form->setModel($m);
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet('Foobar'))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs');
            $fs->add(new Vps_Form_Field_TextField('asdf', 'Asdf'));
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
        $config['assetsType'] = 'Vps_Form_FieldSet_StrageBlueLine:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

