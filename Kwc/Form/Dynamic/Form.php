<?php
class Kwc_Form_Dynamic_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('recipient', trlKwf('E-Mail Recipient')))
            ->setVtype('email');
        $this->add(new Kwf_Form_Field_TextField('recipient_cc', trlVps('E-Mail CC')))
            ->setVtype('email');
        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('E-Mail Subject')));
    }
}