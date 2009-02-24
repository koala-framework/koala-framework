<?php
class Vps_Form_ColorPicker_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_ColorPickerField("hex", "Color"))
            ->setMaxResolution(200);

    }
}

