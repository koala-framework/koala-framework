<?php
class Vpc_Basic_ImageEnlarge_Form extends Vpc_Abstract_Image_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->getByName('linkTag')->setTitle(trlVps('Click on Preview Image').':');

        //$fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Image Texts')));
        //$fs->add(new Vps_Form_Field_TextField('alt', trlVps('Alt-Text')));
    }
}
