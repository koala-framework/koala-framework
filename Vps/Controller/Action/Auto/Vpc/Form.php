<?php
abstract class Vps_Controller_Action_Auto_Vpc_Form extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'saveBack');
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Vpc_Abstract_Form';

    public function preDispatch()
    {
        if (!isset($this->_form)) {
            $this->_form = new $this->_formName(null, $this->class, $this->componentId);
        }
        $this->_form->setBodyStyle('padding: 10px');
        parent::preDispatch();
    }

    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->class)->getExtConfig());
    }
}
