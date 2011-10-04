<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        if (Vpc_Abstract::getSetting($this->getClass(), 'imageTitle')) {
            $this->add(new Vps_Form_Field_TextArea('title', trlVps('Image text')))
                ->setWidth(350)
                ->setHeight(80);
        }
        if (Vpc_Abstract::getSetting($this->getClass(), 'alternativePreviewImage')) {
            $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Alternative Preview Image')))
                    ->setCheckboxToggle(true)
                    ->setCheckboxName('preview_image');

            $fs->add(new Vpc_Abstract_Image_Form('image', $this->getClass()))
                ->setIdTemplate('{0}');
        }

        //absichtlich nicht aufrufen: parent::_initFields();
        //benötigen wir hier nicht, und abgeleitete komponenten können es noch tun
    }
}
