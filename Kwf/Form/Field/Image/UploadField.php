<?php
class Kwf_Form_Field_Image_UploadField extends Kwf_Form_Container_Abstract
{
    const USER_SELECT = 'user';
    const CONTENT_WIDTH = 'contentWidth';

    protected $_dimensions;

    protected $_imageField;
    protected $_dimensionField;
    protected $_imageFileClass = 'Kwf_Form_Field_Image_ImageFile';
    protected $_dimensionFieldClass = 'Kwf_Form_Field_Image_DimensionField';

    public function __construct($imageLabel, $imageUploadRelation, $dimensionColumn)
    {
        parent::__construct();
        $this->setPreviewUrl('/kwf/media/upload/preview-with-crop');
        $dpr2Check = Kwf_Config::getValue('kwc.requireDpr2');
        $this->setXtype('kwf.form.field.image.uploadfield');
        $this->setBaseCls('kwf-form-field-image-upload-big-preview');

        // Fileupload
        $cls = $this->_imageFileClass;
        $this->_imageField = new $cls($imageUploadRelation, $imageLabel);
        $this->_imageField // set to provide big preview image
            ->setPreviewWidth(390)
            ->setPreviewHeight(184)
            ->setCls('kwf-form-field-image-upload-file')
            ->setWidth(390)
            ->setHeight(184);
        $this->fields->add($this->_imageField);

        $cls = $this->_dimensionFieldClass;
        $this->_dimensionField = new $cls($dimensionColumn, trlKwf('Dimension'));
        $this->_dimensionField->setDpr2Check($dpr2Check);
        $this->_dimensionField->setAllowBlank(false)
            ->setLabelStyle('display:none')
            ->setCtCls('kwf-form-field-image-dimension-container');
        $this->fields->add($this->_dimensionField);
    }

    public function getDimensionField()
    {
        return $this->_dimensionField;
    }
    public function getImageField()
    {
        return $this->_imageField;
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
            $requiredWidth = $dimensionsArray[0]['width'] == 'contentWidth' ? trlKwf('Content Width') : $dimensionsArray[0]['width'];
            $requiredRetinaWidth = $dimensionsArray[0]['width'] == 'contentWidth' ? trlKwf('Double Content Width') : $dimensionsArray[0]['width'] * 2;
            $requiredHeight = $dimensionsArray[0]['height'];
            $requiredRetinaHeight = $dimensionsArray[0]['height'] * 2;

            $helptext = trlKwf('Size of Target Image') . ': ' . $requiredWidth . 'x' . $requiredHeight . 'px';
            $helptext .= "<br />" . trlKwf('Size of Target Image to support High Resolution Displays ("Retina")') . ': ' . $requiredRetinaWidth . 'x' . $requiredRetinaHeight . 'px';
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
        $this->_imageField->setAllowBlank($value);
        return $this;
    }

    public function setMaxResolution($maxResolution)
    {
        $this->_imageField->setMaxResolution($maxResolution);
        return $this;
    }
}
