<?php
class Vps_Controller_Action_Welcome_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_tableName = 'Vps_Dao_Welcome';
    protected $_buttons = array('save');
    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setId(1);

        $this->_form->add(new Vps_Form_Field_File('vps_upload_id', 'Welcome-Image', 'WelcomeImage'));
        $this->_form->add(new Vps_Form_Field_File('login_vps_upload_id', 'Login-Image', 'LoginImage'));
        $this->_form->add(new Vps_Form_Field_HtmlEditor('content', 'Content'))
            ->setEnableLinks(false)
            ->setEnableFont(false);

    }

    public function indexAction()
    {
        $this->view->ext('Vps.Auto.FormPanel',
                array('controllerUrl'=>'/vps/welcome/form'));
    }
}
