<?php
class Kwc_News_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $generators = Kwc_Abstract::getSetting($this->getDirectoryClass(), 'generators');
        $this->add(Kwc_Abstract_Form::createComponentForm($generators['detail']['dbIdShortcut'] . '{0}-image'));
    }
}
