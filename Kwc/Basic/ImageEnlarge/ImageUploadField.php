<?php
class Kwc_Basic_ImageEnlarge_ImageUploadField extends Kwc_Abstract_Image_ImageUploadField
{
    public function __construct($imageLabel)
    {
        parent::__construct($imageLabel);
        $this->setXtype('kwc.basic.imageenlarge.imageuploadfield');
    }
}
