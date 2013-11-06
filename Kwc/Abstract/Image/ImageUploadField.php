<?php
class Kwc_Abstract_Image_ImageUploadField extends Kwf_Form_Container_Abstract
{
    private $_image;
    private $_dimensions;

    public function __construct($dimensions, $imageLabel)
    {
        parent::__construct();
        $this->setXtype('kwc.image.imageuploadfield');
        $this->setBaseCls('kwc-abstract-image-image-upload-big-preview');
        // Fileupload
        $this->_image = new Kwf_Form_Field_File('Image', $imageLabel);
        $this->_image->setPreviewSize(100) // set to provide big preview image
            ->setCls('kwc-abstract-image-image-upload-file')
            ->setWidth(423)
            ->setHeight(112)
            ->setAllowOnlyImages(true);
        $this->fields->add($this->_image);

        $this->_dimensions = $dimensions;
        $this->fields->add(new Kwc_Abstract_Image_DimensionField('dimension', trlKwf('Dimension')))
            ->setAllowBlank(false)
            ->setLabelStyle('display:none')
            ->setCtCls('kwc-abstract-image-dimension-container')
            ->setDimensions($dimensions);
    }

    public function setShowHelptext($showHelptext)
    {
        if ($showHelptext) {
            $dimensionsArray = array_values($this->_dimensions);
            $helptext = trlKwf('Size of Target Image') . ': ' . $dimensionsArray[0]['width'] . 'x' . $dimensionsArray[0]['height'] . 'px';
            $helptext .= "<br />" . trlKwf('Size of Target Image to support High Resolution Displays ("Retina")') . ': ' . ($dimensionsArray[0]['width'] * 2) . 'x' . ($dimensionsArray[0]['height'] * 2) . 'px';
            $helptext .= "<br />" . trlKwf('or larger');

            $scaleMethod = trlKwf('don\'t Crop');
            if ($dimensionsArray[0]['cover']) {
                $scaleMethod = trlKwf('Crop');
            }
            $helptext .= "<br />" . trlKwf('If size does not fit, scale method will be') . ': ' . $scaleMethod;
            $this->getByName('Image')->setHelpText($helptext);
        } else {
            $this->getByName('Image')->setHelpText('');
        }
        return $this;
    }

    public function setAllowBlankImage($value)
    {
        $this->_image->setAllowBlank($value);
        return $this;
    }

    public function setMaxResolution($maxResolution)
    {
        $this->_image->setMaxResolution($maxResolution);
        return $this;
    }
}
