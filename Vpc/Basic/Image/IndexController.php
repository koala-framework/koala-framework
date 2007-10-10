<?php
class Vpc_Basic_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vpc_Basic_Image_Form($this->component);
    }
}