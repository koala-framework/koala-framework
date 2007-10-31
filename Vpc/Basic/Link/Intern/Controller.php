<?php
class Vpc_Basic_Link_Intern_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vpc_Basic_Link_Intern_Form($this->component);
        $this->_form->setBodyStyle('padding: 10px');
        parent::preDispatch();
    }
}
