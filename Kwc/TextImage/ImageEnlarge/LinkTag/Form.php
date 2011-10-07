<?php
class Kwc_TextImage_ImageEnlarge_LinkTag_Form extends Kwc_Basic_LinkTag_Form
{
    protected function _init()
    {
        parent::_init();
        $this->fields->first()->setHideLabel(true);
    }
}
