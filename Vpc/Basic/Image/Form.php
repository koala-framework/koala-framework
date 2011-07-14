<?php
class Vpc_Basic_Image_Form extends Vpc_Abstract_Image_Form
{
    protected function _initFields()
    {
        if (!Vpc_Abstract::getSetting($this->getClass(), 'useParentImage')) {
            parent::_initFields();
        }
    }
}
