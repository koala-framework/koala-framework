<?php
class Kwf_Form_Field_Image_ImageFile extends Kwf_Form_Field_File
{
    public function __construct($fieldname = null, $fieldLabel = null)
    {
        parent::__construct($fieldname, $fieldLabel);
        $this->setXtype('kwf.form.field.image.imagefile');
        $this->setAllowOnlyImages(true);
    }
}
