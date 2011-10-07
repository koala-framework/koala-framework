<?php
class Kwc_Posts_Detail_Edit_Form_FrontendForm extends Kwc_Posts_Write_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->fields['content']->setFieldLabel(trlKwfStatic('Edit Post'));
    }
}
