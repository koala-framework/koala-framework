<?php
class Vpc_Form_Field_Select_Trl_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $mf = $this->fields->add(new Vps_Form_Field_MultiFields('Values'));
        $mf->setMinEntries(0);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')));
    }
}