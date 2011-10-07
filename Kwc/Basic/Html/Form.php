<?php
class Kwc_Basic_Html_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->prepend(new Kwf_Form_Field_TextArea('content'))
            ->setFieldLabel(trlKwf('Content'))
            ->setHeight(225)
            ->setWidth(450);
    }
}
