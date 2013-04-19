<?php
class Kwc_Form_Dynamic_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Form Properties'));
        $fs->add(new Kwf_Form_Field_TextField('recipient', trlKwf('E-Mail Recipient')))
            ->setVtype('email')
            ->setWidth(400);
        $fs->add(new Kwf_Form_Field_TextField('recipient_cc', trlKwf('E-Mail CC')))
            ->setVtype('email')
            ->setWidth(400);
        $fs->add(new Kwf_Form_Field_TextField('subject', trlKwf('E-Mail Subject')))
            ->setWidth(400);
        $this->add($fs);
        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-form'));
    }
}