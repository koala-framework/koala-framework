<?php
class Vpc_Basic_Flash_Code_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextArea('code', trlVps('Code')))
            ->setWidth(400)
            ->setHeight(300);
    }
}