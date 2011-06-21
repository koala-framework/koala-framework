<?php
class Vpc_Form_Field_MultiCheckbox_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_Checkbox('show_check_all_links', trlVps('Show check all links')));

        $mf = $this->fields->add(new Vps_Form_Field_MultiFields('Values'));
        $mf->setMinEntries(0);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')));
    }
}