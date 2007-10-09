<?php
class Vpc_Basic_Text_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array('save' => true);

    public function _initFields()
    {
        $this->_form = new Vpc_Basic_Text_Form(null, null, $this->component);
    }
}
