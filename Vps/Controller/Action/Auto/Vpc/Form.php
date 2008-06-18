<?php
abstract class Vps_Controller_Action_Auto_Vpc_Form extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'saveBack');
    protected $_permissions = array('save', 'add');
    protected $_formName;

    public function preDispatch()
    {
        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $this->_form = new $this->_formName(null, $this->class);
            } else {
                $this->_form = Vpc_Abstract_Form::createComponentForm(null, $this->class);
            }
        }
        
        $this->_form->setBodyStyle('padding: 10px');
        $this->_form->setId($this->componentId);
        parent::preDispatch();
    }

    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->class)->getExtConfig());
    }
}
