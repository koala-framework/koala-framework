<?php
class Vpc_Basic_Textfield_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('content', trlVps('Content')))
            ->setWidth(400);
        $this->fields->add(new Vps_Form_Field_ShowField('original_content', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('content');
    }
}
