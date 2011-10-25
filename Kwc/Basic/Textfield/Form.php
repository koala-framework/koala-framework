<?php
class Kwc_Basic_Textfield_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('content', trlKwf('Content')))
            ->setWidth(400);
    }

}
