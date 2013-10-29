<?php
class Kwc_Abstract_Image_ImageUploadField extends Kwf_Form_Container_Abstract
{
    public function __construct($dimensions, $imageLabel, $allowBlankImage, $maxResolution, $showHelptext)
    {
        parent::__construct();
        $this->setBaseCls('imageUploadBigPreview');
        // Fileupload
        $image = new Kwf_Form_Field_File('Image', $imageLabel);
        $image->setAllowBlank($allowBlankImage)
            ->setPreviewSize(100) // set to provide big preview image
            ->setCls('imageUploadFile')
            //->setLabelStyle('display:none')
            ->setCtCls('imageUploadContainer')
            ->setAllowOnlyImages(true);
        if ($maxResolution) {
            $image->setMaxResolution($maxResolution);
        }
        $this->fields->add($image);

        if ($showHelptext) {
            $dimensionsArray = array_values($dimensions);
            $helptext = trlKwf('Size of Target Image') . ': ' . $dimensionsArray[0]['width'] . 'x' . $dimensionsArray[0]['height'] . 'px';
            $helptext .= "<br />" . trlKwf('Size of Target Image to support High Resolution Displays ("Retina")') . ': ' . ($dimensionsArray[0]['width'] * 2) . 'x' . ($dimensionsArray[0]['height'] * 2) . 'px';
            $helptext .= "<br />" . trlKwf('or larger');

            $scaleMethod = trlKwf('don\'t Crop');
            if ($dimensionsArray[0]['cover']) {
                $scaleMethod = trlKwf('Crop');
            }
            $helptext .= "<br />" . trlKwf('If size does not fit, scale method will be') . ': ' . $scaleMethod;
            $this->getByName('Image')->setHelpText($helptext);
        }


        if (count($dimensions) > 1) {
            $this->fields->add(new Kwc_Abstract_Image_DimensionField('dimension', trlKwf('Dimension')))
                ->setAllowBlank(false)
                ->setCls('imageDimension')
                ->setTriggerClass('cropTrigger')
                ->setLabelStyle('display:none')
                ->setCtCls('dimensionContainer')
                ->setDimensions($dimensions);
        }
    }
}
