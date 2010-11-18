<?php
// /vps/test/vps_form_date-field_test
class Vps_Form_DateField_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Model_FnF';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_DateField('blub', 'blub'));
    }

    protected function _getResourceName()
    {
        return 'vps_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Vps_Form_DateField:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

