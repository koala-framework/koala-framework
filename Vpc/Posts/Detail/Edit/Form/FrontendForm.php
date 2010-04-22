<?php
class Vpc_Posts_Detail_Edit_Form_FrontendForm extends Vpc_Posts_Write_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->fields['content']->setFieldLabel(trlVpsStatic('Edit Post'));
    }
}
