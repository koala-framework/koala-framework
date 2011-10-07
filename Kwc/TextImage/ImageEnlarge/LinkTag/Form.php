<?php
class Vpc_TextImage_ImageEnlarge_LinkTag_Form extends Vpc_Basic_LinkTag_Form
{
    protected function _init()
    {
        parent::_init();
        $this->fields->first()->setHideLabel(true);
    }
}
