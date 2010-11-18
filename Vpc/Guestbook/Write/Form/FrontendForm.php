<?php
class Vpc_Guestbook_Write_Form_FrontendForm extends Vpc_Posts_Write_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->insertBefore('content', new Vps_Form_Field_TextField('name', trlVpsStatic('Name')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->insertBefore('content', new Vps_Form_Field_TextField('email', trlVpsStatic('E-Mail')))
            ->setAllowBlank(false)
            ->setVtype('email')
            ->setWidth(300);
    }
}
