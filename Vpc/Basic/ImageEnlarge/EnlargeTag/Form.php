<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        if (Vpc_Abstract::getSetting($this->getClass(), 'alternativePreviewImage')) {
            $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Alternative Preview Image')))
                    ->setCheckboxToggle(true)
                    ->setCheckboxName('preview_image');

            $fs->add(new Vpc_Abstract_Image_Form('image', $this->getClass()))
                ->setIdTemplate('{0}');
        }
    }
}
