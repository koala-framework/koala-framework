<?php
class Kwc_Form_Dynamic_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('submit_caption', trlKwf('Submit Caption')))
            ->setWidth(400);
    }
}
