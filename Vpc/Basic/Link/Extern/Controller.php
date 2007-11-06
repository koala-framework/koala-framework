<?php
class Vpc_Basic_Link_Extern_Controller extends Vpc_Basic_Link_Controller
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vpc_Basic_Link_Extern_Form($this->component);
        $this->_form->setBodyStyle('padding: 10px');
        parent::preDispatch();
    }
}
