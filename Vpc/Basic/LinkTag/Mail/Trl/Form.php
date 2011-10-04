<?php
class Vpc_Basic_LinkTag_Mail_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_EMailField('mail', trlVps('E-Mail Address')))
            ->setWidth(300);
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Predefined Subject for Mail')))
            ->setWidth(300);
        $this->add(new Vps_Form_Field_TextArea('text', trlVps('Predefined Text for Mail')))
            ->setWidth(300)
            ->setHeight(200);

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Master')))
            ->setLabelWidth(160);
        $fs->add(new Vps_Form_Field_ShowField('master_mail', trlVps('E-Mail Address')))
            ->setData(new Vps_Data_Trl_OriginalComponent('mail'));
        $fs->add(new Vps_Form_Field_ShowField('master_subject', trlVps('Predefined Subject for Mail')))
            ->setData(new Vps_Data_Trl_OriginalComponent('subject'));
        $fs->add(new Vps_Form_Field_ShowField('master_text', trlVps('Predefined Text for Mail')))
            ->setData(new Vps_Data_Trl_OriginalComponent('text'));
    }
}
