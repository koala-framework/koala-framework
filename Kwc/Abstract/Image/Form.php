<?php
class Kwc_Abstract_Image_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->_initFieldsUpload();

        // Height, Width
        // add always if multiple dimensions (even if useParentImage)
        $dimensions = Kwc_Abstract::getSetting($this->getClass(), 'dimensions');
        if (count($dimensions) > 1) {
            $this->add(new Kwc_Abstract_Image_DimensionField('dimension', trlKwf('Dimension')))
                ->setAllowBlank(false)
                ->setDimensions($dimensions);
        }

        $this->_initFieldsCaption();
        $this->_initFieldsAltText();

        parent::_initFields();
    }

    protected function _initFieldsUpload()
    {
        // Dateiname
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename')) {
            $this->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
                ->setVtype('alphanum');
        }

        // Fileupload
        $image = new Kwf_Form_Field_File('Image', Kwc_Abstract::getSetting($this->getClass(), 'imageLabel'));
        $image
            ->setAllowBlank(Kwc_Abstract::getSetting($this->getClass(), 'allowBlank'))
            ->setAllowOnlyImages(true);
        if (Kwc_Abstract::getSetting($this->getClass(), 'maxResolution')) {
            $image->setMaxResolution(Kwc_Abstract::getSetting($this->getClass(), 'maxResolution'));
        }
        $this->add($image);

        if (Kwc_Abstract::getSetting($this->getClass(), 'showHelpText')) {
            $dimensions = array_values(Kwc_Abstract::getSetting($this->getClass(), 'dimensions'));
            $helptext = trlKwf('Size of Target Image') . ': ' . $dimensions[0]['width'] . 'x' . $dimensions[0]['height'] . 'px';
            $helptext .= "<br />" . trlKwf('Size of Target Image to support High Resolution Displays ("Retina")') . ': ' . ($dimensions[0]['width'] * 2) . 'x' . ($dimensions[0]['height'] * 2) . 'px';
            $helptext .= "<br />" . trlKwf('or larger');
            $helptext .= "<br />" . trlKwf('If size does not fit, scale method will be') . ': ' . $dimensions[0]['scale'];
            $this->getByName('Image')->setHelpText($helptext);
        }
    }


    protected function _initFieldsCaption()
    {
        // Bildunterschrift
        if (Kwc_Abstract::getSetting($this->getClass(), 'imageCaption')) {
            $this->add(new Kwf_Form_Field_TextField('image_caption', trlKwf('Image caption')))
                ->setWidth(300);
        }
    }

    protected function _initFieldsAltText()
    {
        if (Kwc_Abstract::getSetting($this->getClass(), 'altText')) {
            $this->add(new Kwf_Form_Field_TextField('alt_text', trlKwf('Alt Text')))
                ->setWidth(300);
        }
    }

    public function setFieldLabel($label)
    {
        if ($label) {
            $this->fields['Image']->setFieldLabel($label);
        }
        return $this;
    }
}
