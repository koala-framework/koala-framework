<?php
class Vpc_Simple_Text_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array('save'   => true);
    
    public function _initFields()
    {
        $this->_form->setTable(new Vpc_Simple_Text_IndexModel());
        $this->_form->fields->add(new Vps_Auto_Field_TextArea('content'))
            ->setFieldLabel('Content')
            ->setHeight(225)
            ->setWidth(450);
    }
}
