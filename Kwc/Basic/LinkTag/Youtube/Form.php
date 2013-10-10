<?php
class Kwc_Basic_LinkTag_Youtube_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add($this->createChildComponentForm($this->getClass(), '-video'));
    }
}
