<?php
class Vpc_Composite_TextImage_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vps_Auto_Container();
        $this->_form->add(new Vpc_Basic_Text_Form($this->component->text));
        $this->_form->add(new Vpc_Basic_Image_Form($this->component->image));
    }
}