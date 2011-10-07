<?php
class Kwc_Basic_Flash_Code_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextArea('code', trlKwf('Code')))
            ->setWidth(400)
            ->setHeight(300);
    }
}