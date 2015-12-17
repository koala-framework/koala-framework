<?php
class Kwc_Basic_ImageParentEditable_ImageUploadField extends Kwc_Abstract_Image_ImageUploadField
{
    protected $_imageFileClass = 'Kwc_Basic_ImageParentEditable_ImageFile';

    public function __construct($imageLabel)
    {
        parent::__construct($imageLabel);
        $this->setXtype('kwc.imageparenteditable.imageuploadfield');
    }
}
