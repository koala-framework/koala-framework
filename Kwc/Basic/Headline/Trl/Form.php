<?php
class Kwc_Basic_Headline_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwf('Headline')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_ShowField('original_headline1', trlKwf('Original headline')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline1');
    }
}
