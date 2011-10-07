<?php
class Vpc_News_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $generators = Vpc_Abstract::getSetting($this->getDirectoryClass(), 'generators');
        $this->add(Vpc_Abstract_Form::createComponentForm($generators['detail']['dbIdShortcut'] . '{0}-image'));
    }
}
