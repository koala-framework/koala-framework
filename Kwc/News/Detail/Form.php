<?php
class Kwc_News_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(Kwc_Abstract_Form::createComponentForm('news_{0}-image'));
    }
}
