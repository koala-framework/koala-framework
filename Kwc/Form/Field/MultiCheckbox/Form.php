<?php
class Kwc_Form_Field_MultiCheckbox_Form extends Kwc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_Checkbox('show_check_all_links', trlKwf('Show check all links')));
        $this->insertAfter('required', new Kwf_Form_Field_Select('output_type', trlKwf('Output Type')))
            ->setValues(array(
                'horizontal' => trlKwf('horizontal'),
                'vertical' => trlKwf('vertical')
            ));
        
        $mf = $this->fields->add(new Kwf_Form_Field_MultiFields('Values'));
        $mf->setMinEntries(0);
        $mf->fields->add(new Kwf_Form_Field_TextField('value', trlKwf('Value')));
    }
}