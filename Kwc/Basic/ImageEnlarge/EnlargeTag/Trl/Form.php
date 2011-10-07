<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Form extends Vpc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $imageTitle = Vpc_Abstract::getSetting(
            Vpc_Abstract::getSetting($this->getClass(), 'masterComponentClass'),
            'imageTitle'
        );
        if ($imageTitle) {
            $this->add(new Vps_Form_Field_TextArea('title', trlVps('Title')))
                ->setWidth(350)
                ->setHeight(80);
        }
    }
}
