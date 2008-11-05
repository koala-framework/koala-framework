<?php
class Vps_Controller_Action_Welcome_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Util_Model_Welcome';
    protected $_buttons = array('save');
    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setId(1);

        $this->_form->add(new Vps_Form_Field_File('WelcomeImage', trlVps('Welcome-Image')));
        $this->_form->add(new Vps_Form_Field_File('LoginImage', trlVps('Login-Image')));
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
