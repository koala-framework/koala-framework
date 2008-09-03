<?php
class Vpc_Posts_Detail_Edit_Form_Form extends Vpc_Posts_Write_Form_Form
{
    protected function _init()
    {
        parent::_init();
        $this->fields['content']->setFieldLabel(trlVps('Edit Post'));
    }
}
