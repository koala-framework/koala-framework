<?php
class Kwc_Abstract_Image_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename')) {
            $this->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
                ->setVtype('alphanum');
        }

        $this->add(new Kwc_Abstract_Image_ImageUploadField(
            Kwc_Abstract::getSetting($this->getClass(), 'dimensions'),
            Kwc_Abstract::getSetting($this->getClass(), 'imageLabel'),
            Kwc_Abstract::getSetting($this->getClass(), 'allowBlank'),
            Kwc_Abstract::getSetting($this->getClass(), 'maxResolution'),
            Kwc_Abstract::getSetting($this->getClass(), 'showHelpText')
        ));

        if (Kwc_Abstract::getSetting($this->getClass(), 'imageCaption')) {
            $this->add(new Kwf_Form_Field_TextField('image_caption', trlKwf('Image caption')))
                ->setWidth(300);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'altText')) {
            $this->add(new Kwf_Form_Field_TextField('alt_text', trlKwf('Alt Text')))
                ->setWidth(300);
        }

        parent::_initFields();
    }

    public function setFieldLabel($label)
    {
        if ($label) {
            $this->fields['Image']->setFieldLabel($label);
        }
        return $this;
    }
}
