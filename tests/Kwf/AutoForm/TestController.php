<?php
class Vps_AutoForm_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_AutoForm_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoForm:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('foo', 'Foo'));
    }

    public function jsonSaveAction()
    {
        parent::jsonSaveAction();
        $session = new Zend_Session_Namespace('Vps_AutoForm_Test');
        if ($session->count == 0) {
            $session->count++;
            $this->view->exception = "error";
            $this->view->success = false;
        }
    }

    public function resetAction()
    {
        $session = new Zend_Session_Namespace('Vps_AutoForm_Test');
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
