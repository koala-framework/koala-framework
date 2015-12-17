<?php
class Kwc_Abstract_Image_ImageUploadField extends Kwf_Form_Container_Abstract
{
    private $_image;
    private $_dimensions;
    protected $_imageFileClass = 'Kwc_Abstract_Image_ImageFile';
    protected $_dimensionFieldClass = 'Kwc_Abstract_Image_DimensionField';

    public function __construct($imageLabel)
    {
        parent::__construct();
        $dpr2Check = Kwf_Config::getValue('kwc.requireDpr2');
        $this->setXtype('kwc.image.imageuploadfield');
        $this->setBaseCls('kwc-abstract-image-image-upload-big-preview');

        // Fileupload
        $cls = $this->_imageFileClass;
        $this->_image = new $cls('Image', $imageLabel);
        $this->_image // set to provide big preview image
            ->setPreviewWidth(390)
            ->setPreviewHeight(184)
            ->setCls('kwc-abstract-image-image-upload-file')
            ->setWidth(390)
            ->setHeight(184);
        $this->fields->add($this->_image);

        $cls = $this->_dimensionFieldClass;
        $this->_dimensionField = new $cls('dimension', trlKwf('Dimension'));
        $this->_dimensionField->setDpr2Check($dpr2Check);
        $this->_dimensionField->setAllowBlank(false)
            ->setLabelStyle('display:none')
            ->setCtCls('kwc-abstract-image-dimension-container');
        $this->fields->add($this->_dimensionField);
    }

    public function setDimensions($dimensions)
    {
        $this->_dimensions = $dimensions;
        $this->_dimensionField->setDimensions($dimensions);
        return $this;
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

    public function setSelectDimensionDisabled($disable)
    {
        $this->_dimensionField->setSelectDimensionDisabled($disable);
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
