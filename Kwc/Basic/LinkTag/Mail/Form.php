<?php
class Kwc_Basic_LinkTag_Mail_Form extends Kwc_Abstract_Form
{
    public function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_EMailField('mail', trlKwf('E-Mail address')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('Subject of E-Mail')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextArea('text', trlKwf('Predefined text of E-Mail')))
            ->setWidth(300)
            ->setHeight(200);
    }
}
