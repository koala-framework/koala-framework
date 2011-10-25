<?php
class Kwc_Basic_Html_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextArea('content', trlKwf('Content')))
            ->setHeight(225)
            ->setWidth(450);
        $this->fields->add(new Kwf_Form_Field_ShowField('original_content', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('content');
    }
}
