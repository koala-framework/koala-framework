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

        $masterCC = Kwc_Abstract::getSetting($this->getClass(), 'masterComponentClass');
        if (Kwc_Abstract::getSetting($masterCC, 'imageCaption')) {
            $this->add(new Kwf_Form_Field_ShowField('original_image_caption', trlKwf('Original Image caption')))
                ->setData(new Kwf_Data_Trl_OriginalComponent('image_caption'));
            $this->add(new Kwf_Form_Field_TextField('image_caption', trlKwf('Image caption')))
                ->setWidth(300);
        }

        if (Kwc_Abstract::getSetting($masterCC, 'editFilename') || Kwc_Abstract::getSetting($masterCC, 'altText') || Kwc_Abstract::getSetting($masterCC, 'titleText')) {
            $fs = $this->add(new Kwf_Form_Container_FieldSet('SEO'));
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);
        }
        if (Kwc_Abstract::getSetting($masterCC, 'editFilename')) {
            $fs->add(new Kwf_Form_Field_ShowField('original_filename', trlKwf('Original {0}', trlKwf('Filename'))))
                ->setData(new Kwf_Data_Trl_OriginalComponent('filename'));
            $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename'))) //no trl
                ->setWidth(300)
                ->setHelpText(trlKwf('Talking filename ("lorem-ipsum-2015"), hyphens and underscores are allowed.'));
        }
        if (Kwc_Abstract::getSetting($masterCC, 'altText')) {
            $fs->add(new Kwf_Form_Field_ShowField('original_alt_text', trlKwf('Original {0}', 'ALT Text')))
                ->setData(new Kwf_Data_Trl_OriginalComponent('alt_text'));
            $fs->add(new Kwf_Form_Field_TextField('alt_text', 'ALT Text')) //no trl
                ->setWidth(300)
                ->setHelpText(trlKwf('Short, meaningful description of the image content.'));
        }
        if (Kwc_Abstract::getSetting($masterCC, 'titleText')) {
            $fs->add(new Kwf_Form_Field_ShowField('original_title_text', trlKwf('Original {0}', 'ALT Text')))
                ->setData(new Kwf_Data_Trl_OriginalComponent('title_text'));
            $fs->add(new Kwf_Form_Field_TextField('title_text', 'IMG Title')) //no trl
                ->setWidth(300)
                ->setHelpText(trlKwf('Some browsers show the text as a tooltip when the mouse pointer is hovering the image.'));
        }

        $this->add(new Kwf_Form_Field_ShowField('image', trlKwf('Original Image')))
            ->setData(new Kwc_Abstract_Image_Trl_Form_ImageData());
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Own Image')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_image');
        $fs->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-image', 'image'));
    }
}
