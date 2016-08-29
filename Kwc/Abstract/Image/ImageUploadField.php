<?php
class Kwc_Abstract_Image_ImageUploadField extends Kwf_Form_Field_Image_UploadField
{
    protected $_imageFileClass = 'Kwc_Abstract_Image_ImageFile';

    public function __construct($imageLabel)
    {
        parent::__construct($imageLabel, 'Image', 'dimension');
    }
}
