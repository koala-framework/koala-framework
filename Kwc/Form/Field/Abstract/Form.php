<?php
class Kwc_Form_Field_Abstract_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('field_label', trlKwf('Label')));
        $this->fields->add(new Kwf_Form_Field_NumberField('label_width', trlKwf('Label Width')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);
        $this->fields->add(new Kwf_Form_Field_Checkbox('required', trlKwf('Required')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('hide_label', trlKwf('Hide Label')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('label_position_above', trlKwf('Label above field')));
    }
}
