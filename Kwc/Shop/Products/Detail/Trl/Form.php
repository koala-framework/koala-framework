<?php
class Kwc_Shop_Products_Detail_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_ShowField('original_title', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('title'));
        parent::_initFields();

    }
}
