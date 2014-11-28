<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $checkbox = new Kwf_Form_Field_Checkbox('use_crop', '');
        $checkbox->setLabelSeparator('');
        $checkbox->setCls('kwc-basic-imageenlarge-enlargetag-checkbox-usecrop');
        $checkbox->setBoxLabel(trlKwf('Use crop region'));
        $this->add($checkbox);
        if (Kwc_Abstract::getSetting($this->getClass(), 'imageTitle')) {
            $this->add(new Kwf_Form_Field_TextArea('title', trlKwf('Image text')))
                ->setWidth(350)
                ->setHeight(80);
        }

        parent::_initFields();
    }
}
