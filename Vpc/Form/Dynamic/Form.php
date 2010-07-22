<?php
class Vpc_Form_Dynamic_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('recipient', trlVps('E-Mail Recipient')))
            ->setVtype('email');
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('E-Mail Subject')));
    }
}