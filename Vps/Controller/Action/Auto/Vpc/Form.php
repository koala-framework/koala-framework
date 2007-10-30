<?php
abstract class Vps_Controller_Action_Auto_Vpc_Form extends Vps_Controller_Action_Auto_Form
{
    public function preDispatch()
    {
        if (!isset($this->_form)) {
            $this->_form = new Vps_Auto_Vpc_Form($this->component);
            $this->_form->setBodyStyle('padding: 10px');
        }
        parent::preDispatch();
    }

    public function indexAction()
    {
       $this->view->ext($this->component, $this->_form->getProperties());
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

}
