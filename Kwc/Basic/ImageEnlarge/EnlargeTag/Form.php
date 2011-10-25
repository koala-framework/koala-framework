<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        if (Kwc_Abstract::getSetting($this->getClass(), 'imageTitle')) {
            $this->add(new Kwf_Form_Field_TextArea('title', trlKwf('Image text')))
                ->setWidth(350)
                ->setHeight(80);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'alternativePreviewImage')) {
            $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Alternative Preview Image')))
                    ->setCheckboxToggle(true)
                    ->setCheckboxName('preview_image');

            $fs->add(new Kwc_Abstract_Image_Form('image', $this->getClass()))
                ->setIdTemplate('{0}');
        }

        parent::_initFields();
    }
}
