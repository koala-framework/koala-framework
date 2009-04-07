<?php
// /vps/test/vps_form_color-field_test
class Vps_Form_ColorField_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_ColorField_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_ColorField('color', 'color'));
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
        $config['assetsType'] = 'Vps_Form_ColorField:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

