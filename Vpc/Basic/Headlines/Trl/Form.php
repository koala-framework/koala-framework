<?php
class Vpc_Basic_Headlines_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('headline1', trlVps('Headline 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Vps_Form_Field_TextField('headline2', trlVps('Headline 2')))
            ->setWidth(450);
        $this->fields->add(new Vps_Form_Field_ShowField('original_headline1', trlVps('Original headline 1')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline1');
        $this->fields->add(new Vps_Form_Field_ShowField('original_headline2', trlVps('Original headline 2')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('headline2');
    }
}
