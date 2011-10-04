<?php
class Vpc_Basic_LinkTag_Mail_Form extends Vpc_Abstract_Form
{
    public function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_EMailField('mail', trlVps('E-Mail address')))
            ->setWidth(300);
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Subject of E-Mail')))
            ->setWidth(300);
        $this->add(new Vps_Form_Field_TextArea('text', trlVps('Predefined text of E-Mail')))
            ->setWidth(300)
            ->setHeight(200);
    }
}
