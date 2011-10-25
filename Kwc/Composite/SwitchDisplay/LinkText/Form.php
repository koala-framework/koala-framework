<?php
class Kwc_Composite_SwitchDisplay_LinkText_Form extends Kwc_Basic_Textfield_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->getByName('content')->setFieldLabel(trlKwf('Link text'));
    }
}
