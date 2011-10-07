<?php
class Kwc_Form_Field_Radio_Form extends Kwc_Form_Field_Select_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['width']);
        $this->insertAfter('required', new Kwf_Form_Field_Select('output_type', trlKwf('Output Type')))
            ->setValues(array(
                'horizontal' => trlKwf('horizontal'),
                'vertical' => trlKwf('vertical')
            ));
    }
}
