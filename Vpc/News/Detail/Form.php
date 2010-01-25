<?php
class Vpc_News_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(Vpc_Abstract_Form::createComponentForm('news_{0}-image'));
    }
}
