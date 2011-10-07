<?php
class Vpc_Form_Field_Radio_Form extends Vpc_Form_Field_Select_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['width']);
        $this->insertAfter('required', new Vps_Form_Field_Select('output_type', trlVps('Output Type')))
            ->setValues(array(
                'horizontal' => trlVps('horizontal'),
                'vertical' => trlVps('vertical')
            ));
    }
}
