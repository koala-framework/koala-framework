<?php
// /kwf/test/kwf_form_comment_test
class Kwf_Form_Comment_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Model_FnF';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('foo', 'foo'))
            ->setComment('px');
    }

    protected function _getResourceName()
    {
        return 'kwf_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsPackage'] = new Kwf_Assets_Package_TestPackage('Kwf_Form_Comment');
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

