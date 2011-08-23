<?php
class Vpc_Basic_Html_Trl_Form extends Vpc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextArea('content', trlVps('Content')))
            ->setHeight(225)
            ->setWidth(450);
        $this->fields->add(new Vps_Form_Field_ShowField('original_content', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('content');
    }
}
