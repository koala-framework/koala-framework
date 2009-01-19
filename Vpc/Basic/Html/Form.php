<?php
class Vpc_Basic_Html_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->prepend(new Vps_Form_Field_TextArea('content'))
            ->setFieldLabel(trlVps('Content'))
            ->setHeight(225)
            ->setWidth(450);
    }
}
