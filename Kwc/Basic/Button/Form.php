<?php
class Kwc_Basic_Button_Form extends Kwc_Basic_Link_Form
{
    protected function _initFields()
    {
        parent::_initFields(); 
        $styles = Kwc_Abstract::getSetting($this->getClass(), 'styles');
        if (count($styles) > 1) {
            $this->insertAfter('text', new Kwf_Form_Field_Select('style', trlKwfStatic('Style')))
                ->setValues($styles)
                ->setWidth(300);
        }
    }
}
