<?php
class Vpc_Composite_Images_EditController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function _initFields()
    {
        // Image
        $this->_form->add(new Vpc_Basic_Image_Form($this->component));
    }
}