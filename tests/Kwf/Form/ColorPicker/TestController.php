<?php
class Kwf_Form_ColorPicker_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_ColorPickerField("hex", "Color"))
            ->setMaxResolution(200);

    }
}

