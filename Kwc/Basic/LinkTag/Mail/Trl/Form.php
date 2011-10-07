<?php
class Kwc_Basic_LinkTag_Mail_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_EMailField('mail', trlKwf('E-Mail Address')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('Predefined Subject for Mail')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextArea('text', trlKwf('Predefined Text for Mail')))
            ->setWidth(300)
            ->setHeight(200);

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Master')))
            ->setLabelWidth(160);
        $fs->add(new Kwf_Form_Field_ShowField('master_mail', trlKwf('E-Mail Address')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('mail'));
        $fs->add(new Kwf_Form_Field_ShowField('master_subject', trlKwf('Predefined Subject for Mail')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('subject'));
        $fs->add(new Kwf_Form_Field_ShowField('master_text', trlKwf('Predefined Text for Mail')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('text'));
    }
}
