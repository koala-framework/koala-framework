<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        if (Kwc_Abstract::getSetting($this->getClass(), 'imageTitle')) {
            $this->add(new Kwf_Form_Field_TextArea('title', trlKwf('Image text')))
                ->setWidth(350)
                ->setHeight(80);
        }

        parent::_initFields();
    }
}
