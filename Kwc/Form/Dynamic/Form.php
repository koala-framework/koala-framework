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
        $fs->add(new Kwf_Form_Field_TextField('submit_caption', trlKwf('Submit Caption')))
            ->setWidth(400);
        $this->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Send copy to User'));
        $this->add($fs)
            ->setCheckboxToggle(true)
            ->setCheckboxName('send_confirm_mail');
        $fs->add(new Kwf_Form_Field_Select('confirm_field_component_id', trlKwf('E-Mail Field')))
            ->setAllowBlank(false)
            ->setValues(Kwc_Admin::getInstance($this->getClass())->getControllerUrl('EmailFields').'/json-data');
        $fs->add(new Kwf_Form_Field_TextField('confirm_subject', trlKwf('Subject')))
            ->setWidth(300)
            ->setAllowBlank(false);

        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-form'));
    }
}
