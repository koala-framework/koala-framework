<?php
class Vps_User_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'add');
    protected $_formName = 'Vps_User_UserForm';

    public function init()
    {
        if ($this->_getParam('testDb')) {
            Vps_Test_SeparateDb::setDbAndCreateCookie($this->_getParam('testDb'));
        } else {
            Vps_Test_SeparateDb::setDbFromCookie();
        }
        parent::init();
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->assetsType = 'Vps_User:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
    }
}

