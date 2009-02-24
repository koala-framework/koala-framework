<?php
class Vps_Form_Field_ColorPicker extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('colorpickerfield');
    }
}
