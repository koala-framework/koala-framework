<?php
class Kwc_Basic_Image_Form extends Kwc_Abstract_Image_Form
{
    protected function _initFieldsUpload()
    {
        if (!Kwc_Abstract::getSetting($this->getClass(), 'useParentImage')) {
            parent::_initFieldsUpload();
        }
    }

    protected function _initFieldsAltText()
    {
        if (!Kwc_Abstract::getSetting($this->getClass(), 'useParentImage')) {
            parent::_initFieldsAltText();
        }
    }

    //imageCaption field will be shown if activated
}
