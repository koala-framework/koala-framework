<?php
class Vps_AutoForm_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_AutoForm_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_Form_ShowField:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('foo', 'Foo'));
        $sessionFormId = new Zend_Session_Namespace('test_avoid_reinsert_id');
        if (!isset($sessionFormId->count)) {
            $sessionFormId->count = 0;
        }
    }

    public function jsonSaveAction()
    {
        parent::jsonSaveAction();
        $sessionFormId = new Zend_Session_Namespace('test_avoid_reinsert_id');
        if ($sessionFormId->count == 0) {
            $sessionFormId->count++;
            $this->view->exception = "error";
            $this->view->success = false;
        }
    }

    public function getRowCountAction()
    {
        echo $this->_form->getModel()->countRows();
        exit;
    }

}
