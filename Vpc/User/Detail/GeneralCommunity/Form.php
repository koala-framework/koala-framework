<?php
class Vpc_User_Detail_GeneralCommunity_Form extends Vpc_User_Detail_General_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setTable(Zend_Registry::get('userModel'));

        $this->add(new Vps_Form_Field_TextField('nickname', trlVps('Nickname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $this->add(new Vps_Form_Field_TextField('location', trlVps('Place of living')))
            ->setWidth(250);

        $this->add(new Vps_Form_Field_TextArea('signature', trlVps('Signature')))
            ->setWidth(250)
            ->setHeight(100);

        $this->add(new Vps_Form_Field_TextArea('description_short', trlVps('Short Description')))
            ->setWidth(250)
            ->setHeight(100);
    }
}
