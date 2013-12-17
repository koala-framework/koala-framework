<?php
class Kwc_Basic_Headline_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwf('Text 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('headline2', trlKwf('Text 2')))
            ->setWidth(450);
        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline1', trlKwf('Original text 1')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline1');
        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline2', trlKwf('Original text 2')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline2');
    }
}
