<?php
class Vpc_Form_Field_MultiCheckbox_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_Checkbox('show_check_all_links', trlVps('Show check all links')));
        $this->insertAfter('required', new Vps_Form_Field_Select('output_type', trlVps('Output Type')))
            ->setValues(array(
                'horizontal' => trlVps('horizontal'),
                'vertical' => trlVps('vertical')
            ));
        
        $mf = $this->fields->add(new Vps_Form_Field_MultiFields('Values'));
        $mf->setMinEntries(0);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')));
    }
}