<?php
class Kwc_Mail_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('Subject')))
            ->setAllowBlank(false)
            ->setWidth(300);

        $this->add(new Kwf_Form_Field_ShowField('original_subject', trlKwf('Original Subject')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('subject');
    }
}
