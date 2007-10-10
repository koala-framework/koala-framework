<?php
abstract class Vps_Controller_Action_Auto_Form_Vpc extends Vps_Controller_Action_Auto_Form
{
    public function preDispatch()
    {
        $this->_form = new Vps_Auto_Vpc_Form($this->component);
        $this->_initFields();
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
