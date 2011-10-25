<?php
// /kwf/test/kwf_form_date-field_test
class Kwf_Form_DateField_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Model_FnF';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_DateField('blub', 'blub'));
    }

    protected function _getResourceName()
    {
        return 'kwf_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Kwf_Form_DateField:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

