<?php
class Kwc_Basic_Headlines_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwf('Headline 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('headline2', trlKwf('Headline 2')))
            ->setWidth(450);
        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline1', trlKwf('Original headline 1')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline1');
        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline2', trlKwf('Original headline 2')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline2');
    }
}
