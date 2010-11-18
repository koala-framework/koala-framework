<?php
class Vpc_Menu_Abstract_FormController extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_form->setId($this->_getParam('id'));
    }
}
