<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Form extends Kwc_Abstract_Image_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $imageTitle = Kwc_Abstract::getSetting(
            Kwc_Abstract::getSetting($this->getClass(), 'masterComponentClass'),
            'imageTitle'
        );
        if ($imageTitle) {
            $this->add(new Kwf_Form_Field_ShowField('original_title', trlKwf('Original Title')))
                ->setData(new Kwf_Data_Trl_OriginalComponent('title'));
            $this->add(new Kwf_Form_Field_TextArea('title', trlKwf('Title')))
                ->setWidth(350)
                ->setHeight(80);
        }
    }
}
