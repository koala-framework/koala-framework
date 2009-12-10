<?php
class Vpc_Composite_SwitchDisplay_LinkText_Form extends Vpc_Basic_Textfield_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->getByName('content')->setFieldLabel(trlVps('Link text'));
    }
}
