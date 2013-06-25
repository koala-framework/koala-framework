<?php
class Kwc_Abstract_Image_Trl_Form_ImageData extends Kwc_Abstract_Image_Trl_ImageData
{
    public function load($row)
    {
        $src = $this->_getMasterImageUrl($row->component_id);
        if ($src) {
            return "<img src=\"$src\" />";
        }
        return '';
    }
}

class Kwc_Abstract_Image_Trl_Form extends Kwc_Abstract_Form //nicht von Kwc_Abstract_Composite_Trl_Form, da sonst die felder doppelt eingefügt werden
{
    protected function _initFields()
    {
        parent::_initFields();

        $imageCaption = Kwc_Abstract::getSetting(
            Kwc_Abstract::getSetting($this->getClass(), 'masterComponentClass'),
            'imageCaption'
        );
        if ($imageCaption) {
            $this->add(new Kwf_Form_Field_ShowField('original_image_caption', trlKwf('Original Image caption')))
                ->setData(new Kwf_Data_Trl_OriginalComponent('image_caption'));
            $this->add(new Kwf_Form_Field_TextField('image_caption', trlKwf('Image caption')));
        }

        $this->add(new Kwf_Form_Field_ShowField('image', trlKwf('Original Image')))
            ->setData(new Kwc_Abstract_Image_Trl_Form_ImageData());
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Own Image')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_image');
        $fs->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-image', 'image'));
    }
}
