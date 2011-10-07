<?php
class Kwf_Form_Field_Abstract_Form extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('field_label', trlKwf('Label')))
            ->setWidth(200);
        $this->add(new Kwf_Form_Field_Checkbox('allow_blank', trlKwf('Allow Blank')));
    }
}
