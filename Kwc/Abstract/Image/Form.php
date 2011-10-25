<?php
class Kwc_Abstract_Image_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->_initFieldsUpload();
        $this->_initFieldsCaption();

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
            $dimensions = Kwc_Abstract::getSetting($this->getClass(), 'dimensions');
            $helptext = trlKwf('Size of Target Image') . ': ' . $dimensions[0]['width'] . 'x' . $dimensions[0]['height'] . 'px';
            $helptext .= "<br />" . trlKwf('If size does not fit, scale method will be') . ': ' . $dimensions[0]['scale'];
            $this->getByName('Image')->setHelpText($helptext);
        }

        // Höhe, Breite
        $dimensions = Kwc_Abstract::getSetting($this->getClass(), 'dimensions');
        if (count($dimensions) > 1) {
            $this->add(new Kwc_Abstract_Image_DimensionField('dimension', trlKwf('Dimension')))
                ->setAllowBlank(false)
                ->setDimensions($dimensions);
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

    public function setFieldLabel($label)
    {
        if ($label) {
            $this->fields['Image']->setFieldLabel($label);
        }
        return $this;
    }
}
