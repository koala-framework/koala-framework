<?php
class Kwc_Basic_ImageEnlarge_Form extends Kwc_Abstract_Image_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->getByName('linkTag')->setTitle(trlKwf('Click on Preview Image').':');

        //$fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Image Texts')));
        //$fs->add(new Kwf_Form_Field_TextField('alt', trlKwf('Alt-Text')));
    }
}
