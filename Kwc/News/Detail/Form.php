<?php
class Kwc_News_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_createChildComponentForm('-image');
    }
}
