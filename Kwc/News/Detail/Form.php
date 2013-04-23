<?php
class Kwc_News_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add($this->_createChildComponentForm('-image'));
    }
}
