<?php
class Kwc_News_Detail_Trl_Form extends Kwc_News_Detail_Abstract_Trl_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add($this->_createChildComponentForm('-image'));
    }
}
