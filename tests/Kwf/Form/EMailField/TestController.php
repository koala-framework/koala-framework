<?php
// /kwf/test/kwf_form_EMail-field_test
class Kwf_Form_EMailField_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Model_FnF';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_UrlField('blub', 'blub'));
    }

    protected function _getResourceName()
    {
        return 'kwf_test';
    }

    public function indexAction()
    {
        $config = array();
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsPanel'] = new Kwf_Assets_Package_TestPackage('Kwf_Form_EMailField');
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

