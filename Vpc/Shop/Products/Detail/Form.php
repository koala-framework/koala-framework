<?php
class Vpc_Shop_Products_Detail_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->add(new Vps_Form_Field_NumberField('price', trlVps('Price')));
        $this->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $this->add(Vpc_Abstract_Form::createComponentForm('shopProducts_{0}-image'));
        $this->add(Vpc_Abstract_Form::createComponentForm('shopProducts_{0}-text'));
    }
}
