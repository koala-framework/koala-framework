<?php
class Kwc_Form_Field_Abstract_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('field_label', trlKwf('Label')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('required', trlKwf('Required')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('hide_label', trlKwf('Hide Label')));
    }
}
