<?php
class Kwf_Controller_Action_Welcome_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Util_Model_Welcome';
    protected $_buttons = array('save');
    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setId(1);

        $this->_form->add(new Kwf_Form_Field_File('WelcomeImage', trlKwf('Welcome-Image')));
        $this->_form->add(new Kwf_Form_Field_File('LoginImage', trlKwf('Login-Image')));
        $this->_form->add(new Kwf_Form_Field_HtmlEditor('content', 'Content'))
            ->setEnableLinks(false)
            ->setEnableFont(false);

    }

    public function indexAction()
    {
        $this->view->ext('Kwf.Auto.FormPanel',
                array('controllerUrl'=>'/kwf/welcome/form'));
    }
}
