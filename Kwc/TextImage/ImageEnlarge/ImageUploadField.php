<?php
class Kwc_TextImage_ImageEnlarge_ImageUploadField extends Kwc_Abstract_Image_ImageUploadField
{
    public function __construct($imageLabel)
    {
        parent::__construct($imageLabel);
        $this->setXtype('kwc.textimage.imageenlarge.imageuploadfield');
    }
}
