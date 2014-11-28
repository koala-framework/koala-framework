<?php
class Kwf_AutoForm_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_AutoForm_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function indexAction()
    {
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwf_AutoForm');
        $this->view->viewport = 'Kwf.Test.Viewport';
        parent::indexAction();
    }

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('foo', 'Foo'));
    }

    public function jsonSaveAction()
    {
        parent::jsonSaveAction();
        $session = new Kwf_Session_Namespace('Kwf_AutoForm_Test');
        if ($session->count == 0) {
            $session->count++;
            throw new Kwf_Exception('error');
        }
    }

    public function resetAction()
    {
        $session = new Kwf_Session_Namespace('Kwf_AutoForm_Test');
        $session->count = 0;

        $this->_form->getModel()->resetData();
        echo 'OK';
        exit;
    }

    public function getRowCountAction()
    {
        echo $this->_form->getModel()->countRows();
        exit;
    }

}
